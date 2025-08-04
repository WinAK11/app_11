<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="{{ asset('css/epub-reader.css') }}" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
    <script src="https://unpkg.com/epubjs/dist/epub.min.js"></script>
    <title>{{ $ebook->title }} Reader</title>

    <!-- Include EPUB.js -->
    <script src="https://unpkg.com/epubjs/dist/epub.min.js"></script>

</head>

<body>
    <article class="book-reader">
        <input type="checkbox" id="invert" />
        <input type="checkbox" id="fullscreen" />
        <label for="invert"></label>
        <label for="fullscreen"></label>

        <header>
            <a href="#home">Home</a>
            <h1><a href="#">{{ $ebook->title }}</a></h1>
        </header>

        <nav>
            <ul>
                <li><a href="#" id="prev">previous</a></li>
                <li>
                    <select id="toc-select">
                        <option disabled selected>📖 Table of Contents</option>
                    </select>
                </li>
                <li><a href="#" id="next">next</a></li>
            </ul>
        </nav>

        <section>
            <div id="viewer"></div>
        </section>
    </article>

    <script>
        // Initialize the book
        // const book = ePub("/uploads/ebooks/mobydick.epub");
        const book = ePub("{{ asset($ebook->file_path) }}");
        const rendition = book.renderTo("viewer", {
            width: "100%",
            height: "100%",
            allowScriptedContent: true,
        });

        // Load and populate the Table of Contents dropdown
        book.loaded.navigation.then((toc) => {
            const tocSelect = document.getElementById("toc-select");

            toc.toc.forEach((item) => {
                const option = document.createElement("option");
                const label = item.label.length > 50 ?
                    item.label.slice(0, 47).trim() + "..." :
                    item.label;

                option.value = item.href;
                option.textContent = label;
                tocSelect.appendChild(option);
            });

            // Handle TOC selection change
            tocSelect.addEventListener("change", (e) => {
                const href = e.target.value;
                rendition.display(href);
            });
        });

        rendition.display();

        let currentTheme = "light";

        // Inject theme styles into each EPUB content iframe when it's loaded
        rendition.hooks.content.register(function(contents) {
            const doc = contents.document;

            const style = doc.createElement("style");
            style.id = "theme-style";
            doc.head.appendChild(style);

            applyTheme(currentTheme, style, doc);
        });

        // Handle checkbox toggle
        const invertToggle = document.getElementById("invert");

        invertToggle.addEventListener("change", (e) => {
            currentTheme = e.target.checked ? "dark" : "light";

            rendition.getContents().forEach((contents) => {
                const doc = contents.document;
                const style = doc.getElementById("theme-style");
                applyTheme(currentTheme, style, doc);
            });

        });

        // Apply theme styles inside the book (iframe content)
        function applyTheme(theme, styleElement, doc) {
            if (theme === "dark") {
                styleElement.textContent = `
            body {
                background: #121212 !important;
                color: #e0e0e0 !important;
            }
            a { color: #90caf9 !important; }
        `;
            } else {
                styleElement.textContent = `
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            a { color: #007bff !important; }
        `;
            }
        }

        const fullscreenToggle = document.getElementById("fullscreen");

        fullscreenToggle.addEventListener("change", (e) => {
            const flowMode = e.target.checked ? "scrolled" : "paginated";

            // Reconfigure the rendition
            rendition.flow(flowMode);

            // Optionally resize to fit screen changes
            rendition.resize();
        });

        // Navigation
        document.getElementById("prev").addEventListener("click", (e) => {
            e.preventDefault();
            rendition.prev();
        });

        document.getElementById("next").addEventListener("click", (e) => {
            e.preventDefault();
            rendition.next();
        });

        // Table of Contents
        document.getElementById("toc").addEventListener("click", (e) => {
            e.preventDefault();
            book.loaded.navigation.then((toc) => {
                const tocList = toc.toc.map(item =>
                    `<li><a href="#" data-href="${item.href}">${item.label}</a></li>`).join("");
                const tocWindow = window.open("", "Table of Contents", "width=300,height=600");
                tocWindow.document.write(`<ul>${tocList}</ul>`);
                tocWindow.document.querySelectorAll('a[data-href]').forEach(link => {
                    link.addEventListener("click", (ev) => {
                        ev.preventDefault();
                        rendition.display(link.dataset.href);
                        tocWindow.close();
                    });
                });
            });
        });
    </script>
</body>

</html>
