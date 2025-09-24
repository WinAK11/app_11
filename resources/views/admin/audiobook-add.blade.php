@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Extract Chapters from eBook</h3>
                <ul class="breadcrumbs flex items-center gap10">
                    <li><a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><a href="#">
                            <div class="text-tiny">Audiobooks</div>
                        </a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">Create Audiobook</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <form id="audiobook-form" class="form-new-product form-style-1" method="POST"
                    action="{{ route('admin.audiobook.store') }}">
                    @csrf

                    <fieldset class="ebook-select">
                        <div class="body-title mb-10">Select eBook <span class="tf-color-1">*</span></div>
                        <div class="select">
                            <select name="ebook_id" id="ebook_id" required>
                                <option value="">Choose an eBook to convert</option>
                                @foreach ($ebooks as $ebook)
                                    <option value="{{ $ebook->id }}" data-file-path="{{ $ebook->file_path }}"
                                        data-format="{{ $ebook->format }}"
                                        {{ old('ebook_id') == $ebook->id ? 'selected' : '' }}>
                                        {{ $ebook->title }} by {{ $ebook->author }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('ebook_id')
                            <span class="alert alert-danger">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <!-- Progress and Status Display -->
                    <fieldset id="extraction-status" style="display: none;">
                        <div class="body-title mb-10">Chapter Extraction Progress</div>
                        <div class="progress-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                            <div class="progress-bar"
                                style="background: #e9ecef; height: 20px; border-radius: 10px; overflow: hidden;">
                                <div id="progress-fill"
                                    style="background: #007bff; height: 100%; width: 0%; transition: width 0.3s;"></div>
                            </div>
                            <div id="progress-text" style="margin-top: 10px; font-size: 14px; color: #6c757d;">
                                Ready to extract chapters...
                            </div>
                            <div id="chapter-count" style="margin-top: 5px; font-size: 12px; color: #6c757d;">
                                <!-- Chapter count will be displayed here -->
                            </div>
                        </div>
                    </fieldset>

                    <!-- Chapter Preview -->
                    <fieldset id="chapter-preview" style="display: none;">
                        <div class="body-title mb-10">Chapter Preview</div>
                        <div id="chapters-container"
                            style="max-height: 400px; overflow-y: auto; border: 1px solid #e9ecef; border-radius: 8px; padding: 15px;">
                            <!-- Chapters will be displayed here -->
                        </div>
                    </fieldset>

                    <div class="justify-content-end" style="margin-top: 20px;">
                        <button id="extract-btn" class="tf-button w208" type="button" disabled>
                            <i class="icon-download"></i> Extract Chapters
                        </button>
                        <button id="save-btn" class="tf-button w208" type="submit"
                            style="display: none; margin-left: 10px;">
                            <i class="icon-save"></i> Save Audiobook
                        </button>
                    </div>

                    <!-- Hidden field to store extracted chapters -->
                    <input type="hidden" id="chapters-data" name="chapters_data" value="">
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/epub.js/0.3.93/epub.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ebookSelect = document.getElementById('ebook_id');
            const extractBtn = document.getElementById('extract-btn');
            const saveBtn = document.getElementById('save-btn');
            const extractionStatus = document.getElementById('extraction-status');
            const chapterPreview = document.getElementById('chapter-preview');
            const progressFill = document.getElementById('progress-fill');
            const progressText = document.getElementById('progress-text');
            const chapterCount = document.getElementById('chapter-count');
            const chaptersContainer = document.getElementById('chapters-container');
            const chaptersDataInput = document.getElementById('chapters-data');

            let extractedChapters = [];

            // Enable extract button when ebook is selected
            ebookSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value && selectedOption.dataset.format === 'epub') {
                    extractBtn.disabled = false;
                    extractionStatus.style.display = 'block';
                    resetProgress();
                } else {
                    extractBtn.disabled = true;
                    extractionStatus.style.display = 'none';
                    chapterPreview.style.display = 'none';
                    if (selectedOption.value && selectedOption.dataset.format !== 'epub') {
                        alert('Only EPUB files are supported for audiobook conversion.');
                    }
                }
            });

            // Extract chapters button click
            extractBtn.addEventListener('click', async function() {
                const selectedOption = ebookSelect.options[ebookSelect.selectedIndex];
                if (!selectedOption.value) return;

                const filePath = selectedOption.dataset.filePath;
                await extractChaptersFromEpub(filePath);
            });

            async function extractChaptersFromEpub(filePath) {
                try {
                    updateProgress(0, 'Loading EPUB file...');
                    extractBtn.disabled = true;

                    // Fetch the EPUB file
                    const response = await fetch(`/${filePath}`);
                    if (!response.ok) {
                        throw new Error('Failed to load EPUB file');
                    }

                    const arrayBuffer = await response.arrayBuffer();
                    updateProgress(20, 'Parsing EPUB structure...');

                    // Load the book using epub.js
                    const book = ePub(arrayBuffer);
                    await book.ready;

                    updateProgress(40, 'Extracting chapters...');

                    const spineItems = book.spine.spineItems;
                    const allChapters = [];

                    for (let i = 0; i < spineItems.length; i++) {
                        const section = spineItems[i];
                        const progress = 40 + (i / spineItems.length) * 50;
                        updateProgress(progress, `Processing chapter ${i + 1} of ${spineItems.length}...`);

                        try {
                            const chapterDoc = await book.load(section.href);
                            if (chapterDoc && chapterDoc.body && chapterDoc.body.textContent) {
                                const text = chapterDoc.body.textContent.trim();
                                if (text && text.length > 50) { // Filter out very short sections
                                    allChapters.push({
                                        index: allChapters.length + 1,
                                        title: extractChapterTitle(chapterDoc) ||
                                            `Chapter ${allChapters.length + 1}`,
                                        text: text
                                    });
                                }
                            }
                        } catch (error) {
                            console.warn(`Error reading section ${section.href}:`, error);
                        }
                    }

                    updateProgress(100, 'Chapters extracted successfully!');
                    extractedChapters = allChapters;

                    // Display results
                    displayChapters(allChapters);
                    chapterCount.textContent = `Found ${allChapters.length} chapters`;

                    // Store chapters data for form submission
                    chaptersDataInput.value = JSON.stringify(allChapters);

                    // Show save button
                    saveBtn.style.display = 'inline-block';

                } catch (error) {
                    console.error('Error extracting chapters:', error);
                    updateProgress(0, 'Error: Failed to extract chapters');
                    alert('Failed to extract chapters from EPUB file. Please try another file.');
                } finally {
                    extractBtn.disabled = false;
                }
            }

            function extractChapterTitle(chapterDoc) {
                // Try to find chapter title from various heading tags
                const headings = ['h1', 'h2', 'h3', 'title'];
                for (const tag of headings) {
                    const element = chapterDoc.querySelector(tag);
                    if (element && element.textContent.trim()) {
                        return element.textContent.trim();
                    }
                }
                return null;
            }

            function updateProgress(percentage, text) {
                progressFill.style.width = percentage + '%';
                progressText.textContent = text;
            }

            function resetProgress() {
                updateProgress(0, 'Ready to extract chapters...');
                chapterCount.textContent = '';
                chaptersContainer.innerHTML = '';
                chapterPreview.style.display = 'none';
                saveBtn.style.display = 'none';
                extractedChapters = [];
                chaptersDataInput.value = '';
            }

            function displayChapters(chapters) {
                chaptersContainer.innerHTML = '';

                chapters.forEach((chapter, index) => {
                    const chapterDiv = document.createElement('div');
                    chapterDiv.style.cssText =
                        'border-bottom: 1px solid #e9ecef; padding: 15px 0; margin-bottom: 10px;';

                    const preview = chapter.text.length > 200 ?
                        chapter.text.substring(0, 200) + '...' :
                        chapter.text;

                    chapterDiv.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <h6 style="margin: 0; color: #007bff; font-size: 14px; font-weight: 600;">
                        ${chapter.title}
                    </h6>
                    <span style="background: #e9ecef; padding: 2px 8px; border-radius: 12px; font-size: 11px; color: #6c757d;">
                        ${chapter.text.length} characters
                    </span>
                </div>
                <p style="margin: 0; font-size: 12px; color: #6c757d; line-height: 1.4;">
                    ${preview}
                </p>
            `;

                    chaptersContainer.appendChild(chapterDiv);
                });

                chapterPreview.style.display = 'block';
            }

            // Form submission validation
            document.getElementById('audiobook-form').addEventListener('submit', function(e) {
                if (!chaptersDataInput.value) {
                    e.preventDefault();
                    alert('Please extract chapters first before saving.');
                    return false;
                }
            });
        });
    </script>
@endpush
