<?php
namespace App\Services;

use Aws\Polly\PollyClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PollyService
{
    private $pollyClient;
    private $voiceId;
    private $outputFormat;
    private $sampleRate;

    public function __construct()
    {
        $this->pollyClient = new PollyClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);

        $this->voiceId = env('AWS_POLLY_VOICE_ID', 'Joanna');
        $this->outputFormat = env('AWS_POLLY_OUTPUT_FORMAT', 'mp3');
        $this->sampleRate = env('AWS_POLLY_SAMPLE_RATE', '22050');
    }

    /**
     * Convert text to speech using Amazon Polly
     *
     * @param string $text The text to convert to speech
     * @param string $outputPath The path where the audio file will be saved
     * @param string|null $voiceId Optional voice ID override
     * @return array Result with success status and message
     */
    public function textToSpeech($text, $outputPath, $voiceId = null)
    {
        set_time_limit(90);
        try {
            // Clean and prepare text for Polly
            $cleanText = $this->prepareTextForPolly($text);

            if (empty($cleanText)) {
                return [
                    'success' => false,
                    'message' => 'Text is empty after cleaning'
                ];
            }

            // Use provided voice or default
            $voice = $voiceId ?: $this->voiceId;

            Log::info('Polly TTS Request', [
                'text_length' => strlen($cleanText),
                'voice' => $voice,
                'output_path' => $outputPath
            ]);

            // Split text into chunks if it's too long (Polly has a 3000 character limit for standard voices)
            $chunks = $this->splitTextIntoChunks($cleanText, 2800);
            $audioStreams = [];

            foreach ($chunks as $index => $chunk) {
                $result = $this->pollyClient->synthesizeSpeech([
                    'OutputFormat' => $this->outputFormat,
                    'SampleRate' => $this->sampleRate,
                    'Text' => $chunk,
                    'TextType' => 'text',
                    'VoiceId' => $voice,
                ]);

                $audioStreams[] = $result['AudioStream']->getContents();
            }

            // Combine audio streams if multiple chunks
            $finalAudioContent = $this->combineAudioStreams($audioStreams);

            // Ensure directory exists
            $directory = dirname(public_path($outputPath));
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save the audio file
            $fullPath = public_path($outputPath);
            $bytesWritten = file_put_contents($fullPath, $finalAudioContent);

            if ($bytesWritten === false) {
                throw new \Exception('Failed to write audio file to disk');
            }

            Log::info('Polly TTS Success', [
                'output_path' => $outputPath,
                'file_size' => $bytesWritten,
                'chunks_processed' => count($chunks)
            ]);

            return [
                'success' => true,
                'message' => 'Audio generated successfully',
                'file_path' => $outputPath,
                'file_size' => $bytesWritten,
                'duration_estimate' => $this->estimateAudioDuration($cleanText)
            ];

        } catch (AwsException $e) {
            Log::error('AWS Polly Error', [
                'error' => $e->getMessage(),
                'code' => $e->getAwsErrorCode(),
                'type' => $e->getAwsErrorType()
            ]);

            return [
                'success' => false,
                'message' => 'AWS Polly Error: ' . $e->getMessage(),
                'error_code' => $e->getAwsErrorCode()
            ];

        } catch (\Exception $e) {
            Log::error('Polly Service Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get available voices from Polly
     *
     * @param string|null $languageCode Filter by language code (e.g., 'en-US')
     * @return array
     */
    public function getAvailableVoices($languageCode = null)
    {
        try {
            $params = [];
            if ($languageCode) {
                $params['LanguageCode'] = $languageCode;
            }

            $result = $this->pollyClient->describeVoices($params);

            return [
                'success' => true,
                'voices' => $result['Voices']
            ];

        } catch (AwsException $e) {
            Log::error('Error fetching Polly voices', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'voices' => []
            ];
        }
    }

    /**
     * Prepare text for Polly by cleaning and formatting
     *
     * @param string $text
     * @return string
     */
    private function prepareTextForPolly($text)
    {
        // Remove excessive whitespace and line breaks
        $text = preg_replace('/\s+/', ' ', $text);

        // Remove HTML tags if any
        $text = strip_tags($text);

        $text = str_replace('—', '<break time="500ms"/>', $text);

        // Remove special characters that might cause issues
        $text = preg_replace('/[^\w\s\.,!?;:()\-"\']/', ' ', $text);

        // Clean up multiple spaces
        $text = preg_replace('/\s+/', ' ', $text);

        // Trim
        $text = trim($text);

        // Add proper pauses for readability
        // $text = str_replace(['. ', '! ', '? '], ['. <break time="0.5s"/> ', '! <break time="0.5s"/> ', '? <break time="0.5s"/> '], $text);

        return $text;


    }

    /**
     * Split text into chunks for Polly processing
     *
     * @param string $text
     * @param int $maxLength
     * @return array
     */
    private function splitTextIntoChunks($text, $maxLength = 2800)
    {
        if (strlen($text) <= $maxLength) {
            return [$text];
        }

        $chunks = [];
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $currentChunk = '';

        foreach ($sentences as $sentence) {
            if (strlen($currentChunk . ' ' . $sentence) > $maxLength) {
                if (!empty($currentChunk)) {
                    $chunks[] = trim($currentChunk);
                    $currentChunk = $sentence;
                } else {
                    // If a single sentence is too long, split it by words
                    $words = explode(' ', $sentence);
                    $wordChunk = '';

                    foreach ($words as $word) {
                        if (strlen($wordChunk . ' ' . $word) > $maxLength) {
                            if (!empty($wordChunk)) {
                                $chunks[] = trim($wordChunk);
                                $wordChunk = $word;
                            }
                        } else {
                            $wordChunk .= (empty($wordChunk) ? '' : ' ') . $word;
                        }
                    }

                    if (!empty($wordChunk)) {
                        $currentChunk = $wordChunk;
                    }
                }
            } else {
                $currentChunk .= (empty($currentChunk) ? '' : ' ') . $sentence;
            }
        }

        if (!empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    /**
     * Combine multiple audio streams (simple concatenation)
     *
     * @param array $audioStreams
     * @return string
     */
    private function combineAudioStreams($audioStreams)
    {
        if (count($audioStreams) === 1) {
            return $audioStreams[0];
        }

        // For MP3, we can simply concatenate the binary data
        // Note: This is a simple approach. For production, you might want to use FFmpeg
        return implode('', $audioStreams);
    }

    /**
     * Estimate audio duration based on text length
     *
     * @param string $text
     * @return int Duration in seconds
     */
    private function estimateAudioDuration($text)
    {
        // Average speaking rate is about 150-160 words per minute
        $wordCount = str_word_count($text);
        $wordsPerMinute = 150;
        $durationMinutes = $wordCount / $wordsPerMinute;

        return (int) ($durationMinutes * 60);
    }

    /**
     * Test Polly connection and configuration
     *
     * @return array
     */
    public function testConnection()
    {
        try {
            $result = $this->pollyClient->describeVoices([
                'LanguageCode' => 'en-US'
            ]);

            return [
                'success' => true,
                'message' => 'Polly connection successful',
                'voice_count' => $result['Voices'],
            ];

        } catch (AwsException $e) {
            return [
                'success' => false,
                'message' => 'Polly connection failed: ' . $e->getMessage()
            ];
        }
    }
}
