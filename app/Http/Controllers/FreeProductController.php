<?php
namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Ebook;
use App\Services\PollyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FreeProductController extends Controller {
    public function index( Request $request ) {
        // Get filter parameters
        $order = $request->query( 'order', -1 );
        $f_categories = $request->query( 'categories', '' );

        // Get all categories that have ebooks
        $categories = Category::whereHas( 'ebooks' )->withCount( 'ebooks' )->orderBy( 'name', 'ASC' )->get();

        // Start building the query
        $query = Ebook::query();

        // Apply category filter if provided
        if ( $f_categories != '' ) {
            $categoryIds = explode( ',', $f_categories );
            $query->whereIn( 'category_id', $categoryIds );
        }

        // Apply sorting
        switch ( $order ) {
            case 1: // Title Z-A
            $query->orderBy( 'title', 'DESC' );
            break;
            case 2: // Author A-Z
            $query->orderBy( 'author', 'ASC' );
            break;
            case 3: // Author Z-A
            $query->orderBy( 'author', 'DESC' );
            break;
            case -1: // Title A-Z ( default )
            default:
            $query->orderBy( 'title', 'ASC' );
            break;
        }

        // Load relationships and paginate
        $ebooks = $query->with( 'category' )->paginate( 12 );

        return view( 'free-products', compact(
            'ebooks',
            'categories',
            'order',
            'f_categories'
        ) );
    }

    public function ebooks() {
        $ebooks = Ebook::orderBy( 'id', 'ASC' )->paginate( 10 );
        // dd( $ebooks );
        return view( 'admin.ebooks', compact( 'ebooks' ) );
    }

    public function ebook_add() {
        $authors = Author::all();
        $categories = Category::select( 'id', 'name' )->orderBy( 'name' )->get();
        return view( 'admin.ebook-add', compact( 'authors', 'categories' ) );
    }

    public function ebook_store( Request $request ) {
        $validatedData = $request->validate( [
            'title' => 'required|string|max:255',
            'author_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:epub,pdf',
            'cover_image_data' => 'nullable|string' // base64 cover
        ] );

        $format = strtolower( $request->file( 'file' )->getClientOriginalExtension() );

        // Sanitize folder name from title
        // $folderName = Str::slug( $validatedData[ 'title' ] );
        // // $basePath = public_path( "uploads/ebooks/$folderName" );

        // // Create folder if missing
        // if ( !file_exists( $basePath ) ) {
        //     mkdir( $basePath, 0755, true );
        // // }

        // // Move EPUB file
        // $ebookName = time() . '_' . $request->file( 'file' )->getClientOriginalName();
        // $request->file( 'file' )->move( $basePath, $ebookName );
        // $filePath = "uploads/ebooks/$folderName/$ebookName";

        // Decode and save cover image
        $folderName = Str::slug( $validatedData[ 'title' ] );

        // Store EPUB file on S3
        $ebookFile = $request->file( 'file' );
        $ebookName = time() . '_' . $ebookFile->getClientOriginalName();
        $filePath = $ebookFile->storeAs( "uploads/ebooks/{$folderName}", $ebookName, 's3' );
        Storage::disk( 's3' )->setVisibility( $filePath, 'public' );

        $coverPath = null;
        // if ( $request->filled( 'cover_image_data' ) ) {
        if ( $request->filled( 'cover_image_data' ) ) {
            $base64 = $request->cover_image_data;
            // $imageData = base64_decode( preg_replace( '#^data:image/\w+;base64,#i', '', $base64 ) );
            $imageData = base64_decode( preg_replace( '#^data:image/\w+;base64,#i', '', $base64 ) );
            $coverName = time() . '_cover.jpg';
            // $coverFullPath = "$basePath/$coverName";
            // file_put_contents( $coverFullPath, $imageData );
            // $coverPath = "uploads/ebooks/$folderName/$coverName";
            $coverPath = "uploads/ebooks/{$folderName}/{$coverName}";
            Storage::disk( 's3' )->put( $coverPath, $imageData, 'public' );

        }

        // Save to database
        $ebook = Ebook::create( [
            'title' => $validatedData[ 'title' ],
            'author' => $validatedData[ 'author_name' ],
            'category_id' => $validatedData[ 'category_id' ],
            'description' => $validatedData[ 'description' ] ?? '',
            'file_path' => $filePath,
            'cover_path' => $coverPath,
            'format' => $format
        ] );

        Log::info( 'Ebook created', [ 'id' => $ebook->id ] );

        // return response()->json( [
        //     'message' => 'Ebook uploaded successfully!',
        //     'ebook' => $ebook
        // ] );
        return redirect()->route( 'admin.ebooks' )->with( 'status', 'Ebook has been added successfully.' );
    }

    public function ebook_edit( $id ) {
        $ebook = Ebook::findOrFail( $id );
        return view( 'admin.ebook-edit', compact( 'ebook' ) );
    }

    public function ebook_read( $id ) {
        $ebook = Ebook::findOrFail( $id );

        return view( 'epub-reader', compact( 'ebook' ) );
    }

    // public function ebook_update( Request $request ) {
    //     $validatedData = $request->validate( [
    //         'ebook_id' => 'required|integer|exists:ebooks,id',
    //         'title' => 'required|string|max:255',
    //         'author_name' => 'required|string|max:255',
    //         'category' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'file' => 'nullable|file|mimes:epub,pdf',
    //         'cover_image_data' => 'nullable|string',
    // ] );

    //     $ebook = Ebook::findOrFail( $validatedData[ 'ebook_id' ] );

    //     $folderName = Str::slug( $validatedData[ 'title' ] );
    //     $basePath = public_path( "uploads/ebooks/$folderName" );

    //     dd( $ebook, $folderName, $basePath, $request );

    //     if ( !file_exists( $basePath ) ) {
    //         mkdir( $basePath, 0755, true );
    //     }

    //     // Handle file upload
    //     if ( $request->hasFile( 'file' ) ) {
    //         if ( $ebook->file_path && file_exists( public_path( $ebook->file_path ) ) ) {
    //             unlink( public_path( $ebook->file_path ) );
    //         }

    //         $ebookName = time() . '_' . $request->file( 'file' )->getClientOriginalName();
    //         $request->file( 'file' )->move( $basePath, $ebookName );

    //         $ebook->file_path = "uploads/ebooks/$folderName/$ebookName";
    //         $ebook->format = strtolower( $request->file( 'file' )->getClientOriginalExtension() );
    //     }

    //     // Handle cover upload
    //     if ( $request->filled( 'cover_image_data' ) ) {
    //         if ( $ebook->cover_path && file_exists( public_path( $ebook->cover_path ) ) ) {
    //             unlink( public_path( $ebook->cover_path ) );
    //         }

    //         $imageData = base64_decode( preg_replace( '#^data:image/\w+;base64,#i', '', $request->cover_image_data ) );
    //         $coverName = time() . '_cover.jpg';
    //         file_put_contents( "$basePath/$coverName", $imageData );

    //         $ebook->cover_path = "uploads/ebooks/$folderName/$coverName";
    //     }

    //     // Final metadata update
    //     $ebook->title = $validatedData[ 'title' ];
    //     $ebook->author = $validatedData[ 'author_name' ];
    //     $ebook->category = $validatedData[ 'category' ];
    //     $ebook->description = $validatedData[ 'description' ] ?? '';

    //     // Ensure file_path still exists
    //     if ( !$ebook->file_path ) {
    //         return back()->withErrors( [ 'file' => 'Ebook file is required.' ] );
    //     }

    //     $ebook->save();

    //     return redirect()->route( 'admin.ebooks' )->with( 'status', 'Ebook updated successfully!' );
    // }

    public function ebook_delete( $id ) {
        $ebook = Ebook::findOrFail( $id );

        // if ( $ebook->file_path && file_exists( public_path( $ebook->file_path ) ) ) {
        //     unlink( public_path( $ebook->file_path ) );
        // }

        // if ( $ebook->cover_path && file_exists( public_path( $ebook->cover_path ) ) ) {
        //     unlink( public_path( $ebook->cover_path ) );
        // }

        if ( $ebook->file_path && Storage::disk( 's3' )->exists( $ebook->file_path ) ) {
            Storage::disk( 's3' )->delete( $ebook->file_path );
        }

        if ( $ebook->cover_path && Storage::disk( 's3' )->exists( $ebook->cover_path ) ) {
            Storage::disk( 's3' )->delete( $ebook->cover_path );
        }

        // Optionally, delete the entire directory if it's now empty
        $folderName = Str::slug($ebook->title);
        $directory = "uploads/ebooks/{$folderName}";
        if (Storage::disk('s3')->exists($directory) && count(Storage::disk('s3')->files($directory)) === 0) {
            Storage::disk('s3')->deleteDirectory($directory);
        }


        $ebook->delete();
        return back()->with( 'status', 'Ebook deleted!' );

    }

    public function audiobook_add() {
        // Get only EPUB ebooks for audiobook conversion
        $ebooks = Ebook::where( 'format', 'epub' )->orderBy( 'title', 'ASC' )->get();
        return view( 'admin.audiobook-add', compact( 'ebooks' ) );
    }

    public function audiobook_store( Request $request ) {
        $validatedData = $request->validate( [
            'ebook_id' => 'required|exists:ebooks, id',
            'chapters_data' => 'required|string'
        ] );

        try {
            // Get the ebook
            $ebook = Ebook::findOrFail( $validatedData[ 'ebook_id' ] );

            // Decode chapters data
            $chapters = json_decode( $validatedData[ 'chapters_data' ], true );

            if ( !$chapters || !is_array( $chapters ) ) {
                return back()->withErrors( [ 'chapters_data' => 'Invalid chapters data' ] );
            }

            // Check if chapters already exist for this ebook
            $existingChapters = Chapter::where( 'ebook_id', $ebook->id )->count();

            if ( $existingChapters > 0 ) {
                return back()->withErrors( [ 'ebook_id' => 'Chapters already exist for this ebook. Delete existing chapters first.' ] );
            }

            // Store chapters
            foreach ( $chapters as $chapterData ) {
                Chapter::create( [
                    'ebook_id' => $ebook->id,
                    'index' => $chapterData[ 'index' ],
                    'title' => $chapterData[ 'title' ] ?? null,
                    'text' => $chapterData[ 'text' ],
                ] );
            }

            Log::info( 'Audiobook chapters created', [
                'ebook_id' => $ebook->id,
                'chapter_count' => count( $chapters )
            ] );

            return redirect()->route( 'admin.audiobook.chapters', $ebook->id )
            ->with( 'status', 'Chapters extracted successfully! You can now generate audio for each chapter.' );

        } catch ( \Exception $e ) {
            Log::error( 'Error creating audiobook chapters', [
                'error' => $e->getMessage(),
                'ebook_id' => $request->ebook_id ?? null
            ] );

            return back()->withErrors( [ 'error' => 'Failed to create audiobook chapters: ' . $e->getMessage() ] );
        }
    }

    public function audiobook_chapters( $ebook_id ) {
        $ebook = Ebook::findOrFail( $ebook_id );
        $chapters = Chapter::where( 'ebook_id', $ebook_id )
        ->orderBy( 'index', 'ASC' )
        ->get();

        if ( $chapters->isEmpty() ) {
            return redirect()->route( 'admin.audiobook.add' )
            ->with( 'error', 'No chapters found for this ebook. Please extract chapters first.' );
        }

        return view( 'admin.audiobook-chapters', compact( 'ebook', 'chapters' ) );
    }

    public function audiobook_delete_chapters( $ebook_id ) {
        $ebook = Ebook::findOrFail( $ebook_id );

        // Delete all chapters and their audio files
        $chapters = Chapter::where( 'ebook_id', $ebook_id )->get();

        foreach ( $chapters as $chapter ) {
            // Delete audio file if exists
            // if ( $chapter->audio_path && file_exists( public_path( $chapter->audio_path ) ) ) {
            //     unlink( public_path( $chapter->audio_path ) );
            // }
            if ($chapter->audio_path && Storage::disk('s3')->exists($chapter->audio_path)) {
                Storage::disk('s3')->delete($chapter->audio_path);
            }

            $chapter->delete();
        }

        return back()->with( 'status', 'All chapters deleted successfully!' );
    }
    // Method to regenerate chapters ( useful for testing )

    public function audiobook_regenerate_chapters( Request $request, $ebook_id ) {
        $ebook = Ebook::findOrFail( $ebook_id );

        // Delete existing chapters
        $existingChapters = Chapter::where( 'ebook_id', $ebook_id )->get();
        foreach ( $existingChapters as $chapter ) {
            // if ( $chapter->audio_path && file_exists( public_path( $chapter->audio_path ) ) ) {
            //     unlink( public_path( $chapter->audio_path ) );
            // }
            if ($chapter->audio_path && Storage::disk('s3')->exists($chapter->audio_path)) {
                Storage::disk('s3')->delete($chapter->audio_path);
            }
            $chapter->delete();
        }

        // Redirect to add page with the ebook pre-selected
        return redirect()->route( 'admin.audiobook.add' )
        ->with( 'preselect_ebook', $ebook_id )
        ->with( 'status', 'Existing chapters deleted. You can now re-extract chapters.' );
    }

    public function generate_chapter_audio( Request $request ) {
        $validatedData = $request->validate( [
            'chapter_id' => 'required|integer|exists:chapters, id',
            'voice_id' => 'nullable|string'
        ] );

        try {
            $chapter = Chapter::findOrFail( $validatedData[ 'chapter_id' ] );
            $ebook = $chapter->ebook;

            // Initialize Polly service
            $pollyService = new PollyService();

            // Create folder structure
            // $folderName = Str::slug( $ebook->title );
            // $audioFolder = "uploads/audiobooks/{$folderName}";
            // $audioFileName = "chapter_{$chapter->index}_" . time() . '.mp3';
            // $audioPath = "{$audioFolder}/{$audioFileName}";

            // // Ensure directory exists
            // $fullAudioFolder = public_path( $audioFolder );
            // if ( !file_exists( $fullAudioFolder ) ) {
            //     mkdir( $fullAudioFolder, 0755, true );
            // }
            $folderName = Str::slug($ebook->title);
            $audioFileName = "chapter_{$chapter->index}_" . time() . '.mp3';
            $s3Path = "uploads/audiobooks/{$folderName}/{$audioFileName}";

            // Create a temporary local file path
            $tempPath = tempnam(sys_get_temp_dir(), 'polly_audio');


            Log::info( 'Generating audio for chapter', [
                'chapter_id' => $chapter->id,
                'ebook_id' => $ebook->id,
                'text_length' => strlen( $chapter->text ),
                // 'output_path' => $audioPath
                'output_path' => $s3Path
            ] );

            // Generate audio using Polly
            $result = $pollyService->textToSpeech(
                $chapter->text,
                // $audioPath,
                $tempPath,
                $validatedData[ 'voice_id' ] ?? null
            );

            if ( $result[ 'success' ] ) {
                Storage::disk('s3')->put($s3Path, file_get_contents($tempPath), 'public');

                // Clean up the temporary file
                @unlink($tempPath);

                // Update chapter with audio path
                $chapter->update( [
                    // 'audio_path' => $audioPath,
                    'audio_path' => $s3Path,
                    'audio_duration' => $result[ 'duration_estimate' ] ?? null
                ] );

                Log::info( 'Audio generation successful', [
                    'chapter_id' => $chapter->id,
                    'file_size' => $result[ 'file_size' ],
                    'duration' => $result[ 'duration_estimate' ]
                ] );

                return response()->json( [
                    'success' => true,
                    'message' => 'Audio generated successfully',
                    // 'audio_path' => $audioPath,
                    'audio_path' => Storage::disk('s3')->url($s3Path),
                    'file_size' => $result[ 'file_size' ],
                    'duration' => $result[ 'duration_estimate' ]
                ] );

            } else {
                Log::error( 'Audio generation failed', [
                    'chapter_id' => $chapter->id,
                    'error' => $result[ 'message' ]
                ] );

                return response()->json( [
                    'success' => false,
                    'message' => $result[ 'message' ]
                ], 500 );
            }

        } catch ( \Exception $e ) {
            Log::error( 'Chapter audio generation error', [
                'chapter_id' => $validatedData[ 'chapter_id' ],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ] );

            return response()->json( [
                'success' => false,
                'message' => 'Error generating audio: ' . $e->getMessage()
            ], 500 );
        }
    }

    /**
    * Generate audio for all chapters of an ebook
    */

    public function generate_all_chapters_audio( Request $request ) {
        $validatedData = $request->validate( [
            'ebook_id' => 'required|integer|exists:ebooks, id',
            'voice_id' => 'nullable|string'
        ] );

        try {
            $ebook = Ebook::findOrFail( $validatedData[ 'ebook_id' ] );
            $chapters = Chapter::where( 'ebook_id', $ebook->id )
            ->whereNull( 'audio_path' )
            ->orderBy( 'index', 'ASC' )
            ->get();

            if ( $chapters->isEmpty() ) {
                return response()->json( [
                    'success' => false,
                    'message' => 'No chapters found or all chapters already have audio'
                ] );
            }

            $pollyService = new PollyService();
            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ( $chapters as $chapter ) {
                try {
                    // Create audio path
                    // $folderName = Str::slug( $ebook->title );
                    // $audioFolder = "uploads/audiobooks/{$folderName}";
                    $folderName = Str::slug($ebook->title);
                    $audioFileName = "chapter_{$chapter->index}_" . time() . '.mp3';
                    // $audioPath = "{$audioFolder}/{$audioFileName}";

                    // Ensure directory exists
                    // $fullAudioFolder = public_path( $audioFolder );
                    // if ( !file_exists( $fullAudioFolder ) ) {
                    //     mkdir( $fullAudioFolder, 0755, true );
                    // }

                    // Generate audio
                    $s3Path = "uploads/audiobooks/{$folderName}/{$audioFileName}";
                    $tempPath = tempnam(sys_get_temp_dir(), 'polly_audio_bulk');

                    $result = $pollyService->textToSpeech(
                        $chapter->text,
                        // $audioPath,
                        $tempPath,
                        $validatedData[ 'voice_id' ] ?? null
                    );

                    if ( $result[ 'success' ] ) {
                        Storage::disk('s3')->put($s3Path, file_get_contents($tempPath), 'public');
                        @unlink($tempPath);

                        $chapter->update( [
                            // 'audio_path' => $audioPath,
                            'audio_path' => $s3Path,
                            'audio_duration' => $result[ 'duration_estimate' ] ?? null
                        ] );

                        $results[] = [
                            'chapter_id' => $chapter->id,
                            'success' => true,
                            // 'audio_path' => $audioPath
                            'audio_path' => $s3Path
                        ];
                        $successCount++;

                    } else {
                        $results[] = [
                            'chapter_id' => $chapter->id,
                            'success' => false,
                            'error' => $result[ 'message' ]
                        ];
                        $errorCount++;
                        @unlink($tempPath);
                    }

                    // Add a small delay between requests to avoid rate limiting
                    usleep( 500000 );
                    // 0.5 seconds

                } catch ( \Exception $e ) {
                    $results[] = [
                        'chapter_id' => $chapter->id,
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                    $errorCount++;
                    @unlink($tempPath);
                }
            }

            Log::info( 'Bulk audio generation completed', [
                'ebook_id' => $ebook->id,
                'total_chapters' => count( $chapters ),
                'success_count' => $successCount,
                'error_count' => $errorCount
            ] );

            return response()->json( [
                'success' => true,
                'message' => "Audio generation completed. {$successCount} successful, {$errorCount} failed.",
                'results' => $results,
                'summary' => [
                    'total' => count( $chapters ),
                    'successful' => $successCount,
                    'failed' => $errorCount
                ]
            ] );

        } catch ( \Exception $e ) {
            Log::error( 'Bulk audio generation error', [
                'ebook_id' => $validatedData[ 'ebook_id' ],
                'error' => $e->getMessage()
            ] );

            return response()->json( [
                'success' => false,
                'message' => 'Error during bulk generation: ' . $e->getMessage()
            ], 500 );
        }
    }

    /**
    * Get available Polly voices
    */

    public function get_polly_voices( Request $request ) {
        try {
            $pollyService = new PollyService();
            $languageCode = $request->query( 'language', 'en-US' );

            $result = $pollyService->getAvailableVoices( $languageCode );

            if ( $result[ 'success' ] ) {
                return response()->json( [
                    'success' => true,
                    'voices' => $result[ 'voices' ]
                ] );
            } else {
                return response()->json( [
                    'success' => false,
                    'message' => $result[ 'message' ]
                ], 500 );
            }

        } catch ( \Exception $e ) {
            return response()->json( [
                'success' => false,
                'message' => 'Error fetching voices: ' . $e->getMessage()
            ], 500 );
        }
    }

    /**
    * Test Polly configuration
    */

    public function test_polly_connection() {
        try {
            $pollyService = new PollyService();
            $result = $pollyService->testConnection();

            return response()->json( $result );

        } catch ( \Exception $e ) {
            return response()->json( [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500 );
        }
    }


    /**
    * Delete chapter audio file
    */

    public function delete_chapter_audio( $chapter_id ) {
        try {
            $chapter = Chapter::findOrFail( $chapter_id );

            // if ( $chapter->audio_path && file_exists( public_path( $chapter->audio_path ) ) ) {
            //     unlink( public_path( $chapter->audio_path ) );
            // }
            if ($chapter->audio_path && Storage::disk('s3')->exists($chapter->audio_path)) {
                Storage::disk('s3')->delete($chapter->audio_path);
            }

            $chapter->update( [
                'audio_path' => null,
                'audio_duration' => null
            ] );

            return response()->json( [
                'success' => true,
                'message' => 'Audio file deleted successfully'
            ] );

        } catch ( \Exception $e ) {
            return response()->json( [
                'success' => false,
                'message' => 'Error deleting audio: ' . $e->getMessage()
            ], 500 );
        }
    }

    public function generate_voice_preview(Request $request){
        $validatedData = $request->validate([
            'voice_id' => 'required|string',
            'text' => 'required|string|max:500'
        ]);

        try {
            $pollyService = new PollyService();

            // Create preview folder
            $previewFolder = 'uploads/previews';
            $previewFileName = 'preview_' . $validatedData['voice_id'] . '_' . time() . '.mp3';
            // // $previewPath = "{$previewFolder}/{$previewFileName}";

            // // Ensure directory exists
            // $fullPreviewFolder = public_path($previewFolder);
            // if (!file_exists($fullPreviewFolder)) {
            //     mkdir($fullPreviewFolder, 0755, true);
            // }
            $s3Path = "{$previewFolder}/{$previewFileName}";
            $tempPath = tempnam(sys_get_temp_dir(), 'preview_audio');

            Log::info('Generating voice preview', [
                'voice_id' => $validatedData['voice_id'],
                'text_length' => strlen($validatedData['text'])
            ]);

            // Generate preview audio
            $result = $pollyService->textToSpeech(
                $validatedData['text'],
                // $previewPath,
                $tempPath,
                $validatedData['voice_id']
            );

            if ($result['success']) {
                // Schedule file deletion after 1 hour
                // dispatch(function() use ($previewPath) {
                //     if (file_exists(public_path($previewPath))) {
                //         unlink(public_path($previewPath));
                Storage::disk('s3')->put($s3Path, file_get_contents($tempPath), 'public');
                @unlink($tempPath);
                dispatch(function() use ($s3Path) {
                if (Storage::disk('s3')->exists($s3Path)) {
                    Storage::disk('s3')->delete($s3Path);
                }
                })->delay(now()->addHour());

                return response()->json([
                    'success' => true,
                    'message' => 'Preview generated successfully',
                    // 'audio_url' => asset($previewPath),
                    'audio_url' => Storage::disk('s3')->url($s3Path),
                    'duration' => $result['duration_estimate']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
            @unlink($tempPath); // Ensure cleanup on failure


        } catch (\Exception $e) {
            Log::error('Voice preview generation error', [
                'voice_id' => $validatedData['voice_id'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generating preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Voice settings page
     */
    public function voice_settings(){
        return view('admin.voice-settings' );
    }

    /**
 * Get chapters for an ebook (API endpoint)
 */
/**
 * Get chapters for an ebook (API endpoint)
 */
public function getEbookChapters($ebook_id)
{
    try {
        $ebook = Ebook::findOrFail($ebook_id);

        $chapters = Chapter::where('ebook_id', $ebook_id)
            ->whereNotNull('audio_path') // Only return chapters with audio
            ->orderBy('index', 'ASC')
            // ->select(['id', 'index', 'title', 'audio_path'])
            // ->get();
            ->select(['id', 'index', 'title', 'audio_path', 'audio_duration'])
            ->get()
            ->map(function ($chapter) {
                $chapter->audio_path = $chapter->audio_path ? Storage::disk('s3')->url($chapter->audio_path) : null;
                return $chapter;
            });

        return response()->json([
            'success' => true,
            'chapters' => $chapters,
            'ebook' => [
                'id' => $ebook->id,
                'title' => $ebook->title,
                'author' => $ebook->author
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error fetching ebook chapters', [
            'ebook_id' => $ebook_id,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error fetching chapters: ' . $e->getMessage(),
            'chapters' => []
        ], 500);
    }
}

public function ebook_details($id) {
    $ebook = Ebook::with(['category', 'chapters' => function($query) {
        $query->orderBy('index', 'ASC');
    }])->findOrFail($id);

    // Get related ebooks from the same category (excluding current ebook)
    $related_ebooks = Ebook::with('category')
        ->where('category_id', $ebook->category_id)
        ->where('id', ' != ', $ebook->id)
        ->limit(4)
        ->get();

    // If not enough related ebooks from same category, get random ones
    if ($related_ebooks->count() < 4) {
        $additional_ebooks = Ebook::with('category')
            ->where('id', ' != ', $ebook->id)
            ->whereNotIn('id', $related_ebooks->pluck('id'))
            ->inRandomOrder()
            ->limit(4 - $related_ebooks->count())
            ->get();

        $related_ebooks = $related_ebooks->concat($additional_ebooks);
    }

    return view('ebook-details', compact('ebook', 'related_ebooks' ) );
    }
}
