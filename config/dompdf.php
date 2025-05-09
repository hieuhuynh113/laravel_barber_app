<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Set some default values. It is possible to add all defines that can be set
    | in dompdf_config.inc.php. You can also override the entire config file.
    |
    */
    'show_warnings' => false,   // Throw an Exception on warnings from dompdf

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    | Set options for the PDF generation process.
    |
    */
    'options' => [
        /**
         * The root directory for fonts
         */
        'font_dir' => storage_path('fonts/'),

        /**
         * The directory where font cache files should be stored
         */
        'font_cache' => storage_path('fonts/'),

        /**
         * The location of the DOMPDF font directory
         *
         * The location of the directory where DOMPDF will store fonts and font metrics
         * Note: This directory must exist and be writable by the webserver process.
         */
        'chroot' => realpath(base_path()),

        /**
         * Font options
         *
         * dompdf utilizes the DOMPDF font directory to store font metrics and
         * cached font data. This directory must be writable by the webserver
         * process.
         */
        'enable_font_subsetting' => true,

        /**
         * Html rendering options
         */
        'enable_html5_parser' => true,
        'enable_remote' => true,
        'enable_javascript' => true,
        'enable_php' => false,
        'enable_iframe_content' => true,

        /**
         * Encoding
         *
         * Set the default encoding for all text
         */
        'default_encoding' => 'utf-8',

        /**
         * PDF rendering options
         */
        'default_paper_size' => 'a4',
        'default_font' => 'dejavu sans',
        'dpi' => 96,
        'default_view' => 'FitH',
        'font_height_ratio' => 1.1,
        'is_unicode' => true,
        'pdf_backend' => 'CPDF',
        'pdflib_license' => '',
        'text_input_format' => 'utf-8',

        /**
         * Debugging
         */
        'debug_png' => false,
        'debug_keep_temp' => false,
        'debug_css' => false,
        'debug_layout' => false,
        'debug_layout_lines' => false,
        'debug_layout_blocks' => false,
        'debug_layout_inline' => false,
        'debug_layout_padding_box' => false,
    ],

    'defines' => [
        /**
         * The location of the DOMPDF font directory
         *
         * The location of the directory where DOMPDF will store fonts and font metrics
         * Note: This directory must exist and be writable by the webserver process.
         */
        'DOMPDF_FONT_DIR' => storage_path('fonts/'),

        /**
         * The location of the DOMPDF font cache directory
         *
         * This directory contains the cached font metrics. This directory must be writable by the webserver process.
         */
        'DOMPDF_FONT_CACHE' => storage_path('fonts/'),

        /**
         * The location of a temporary directory.
         *
         * The directory specified must be writable by the webserver process.
         * The temporary directory is required to download remote images and when
         * using the PFDLib backend.
         */
        'DOMPDF_TEMP_DIR' => sys_get_temp_dir(),

        /**
         * ==== IMPORTANT ====
         *
         * dompdf's "chroot": Prevents dompdf from accessing system files or other
         * files on the webserver.  All local files opened by dompdf must be in a
         * subdirectory of this directory.  DO NOT set it to '/' since this could
         * allow an attacker to use dompdf to read any files on the server.  This
         * should be an absolute path.
         * This is only checked on command line call by dompdf.php, but not by
         * direct class use like:
         * $dompdf = new DOMPDF();  $dompdf->load_html($htmldata); $dompdf->render(); $pdfdata = $dompdf->output();
         */
        'DOMPDF_CHROOT' => realpath(base_path()),

        /**
         * Whether to use Unicode fonts or not.
         *
         * When set to true the PDF backend must be set to "CPDF" and fonts must be
         * loaded via load_font.php.
         *
         * When enabled, dompdf can support all Unicode glyphs. Any glyphs used in a
         * document must be present in your fonts, however.
         */
        'DOMPDF_UNICODE_ENABLED' => true,

        /**
         * Whether to enable font subsetting or not.
         */
        'DOMPDF_ENABLE_FONT_SUBSETTING' => true,

        /**
         * The PDF rendering backend to use
         *
         * Valid settings are 'PDFLib', 'CPDF' (the bundled R&OS PDF class), 'GD' and
         * 'auto'. 'auto' will look for PDFLib and use it if found, or if not it will
         * fall back on CPDF. 'GD' renders PDFs to graphic files. {@link
         * Canvas_Factory} ultimately determines which rendering class to instantiate
         * based on this setting.
         *
         * Both PDFLib & CPDF rendering backends provide sufficient rendering
         * capabilities for dompdf, however additional features (e.g. object,
         * image and font support, etc.) differ between backends.  Please see
         * {@link PDFLib_Adapter} for more information on the PDFLib backend
         * and {@link CPDF_Adapter} and lib/class.pdf.php for more information
         * on CPDF. Also see the documentation for each backend at the links
         * below.
         *
         * The GD rendering backend is a little different than PDFLib and
         * CPDF. Several features of CPDF and PDFLib are not supported or do
         * not make any sense when creating image files.  For example,
         * multiple pages are not supported, nor are PDF 'objects'.  Have a
         * look at {@link GD_Adapter} for more information.  GD support is
         * experimental, so use it at your own risk.
         *
         * @link http://www.pdflib.com
         * @link http://www.ros.co.nz/pdf
         * @link http://www.php.net/image
         */
        'DOMPDF_PDF_BACKEND' => 'CPDF',

        /**
         * PDFlib license key
         *
         * If you are using a licensed, commercial version of PDFlib, specify
         * your license key here.  If you are using PDFlib-Lite or are evaluating
         * the commercial version of PDFlib, comment out this setting.
         *
         * @link http://www.pdflib.com
         *
         * If pdflib present in web server and auto or selected explicitly above,
         * a real license code must exist!
         */
        'DOMPDF_PDFLIB_LICENSE' => '',

        /**
         * html target media view which should be rendered into pdf.
         * List of types and parsing rules for future extensions:
         * http://www.w3.org/TR/REC-html40/types.html
         *   screen, tty, tv, projection, handheld, print, braille, aural, all
         * Note: aural is deprecated in CSS 2.1 because it is replaced by speech in CSS 3.
         * Note, even though the generated pdf file is intended for print output,
         * the desired content might be different (e.g. screen or projection view of html file).
         * Therefore allow specification of content here.
         */
        'DOMPDF_DEFAULT_MEDIA_TYPE' => 'screen',

        /**
         * The default paper size.
         *
         * North America standard is "letter"; other countries generally "a4"
         *
         * @see CPDF_Adapter::PAPER_SIZES for valid sizes ('letter', 'legal', 'A4', etc.)
         */
        'DOMPDF_DEFAULT_PAPER_SIZE' => 'a4',

        /**
         * The default font family
         *
         * Used if no suitable fonts can be found. This must exist in the font folder.
         * @var string
         */
        'DOMPDF_DEFAULT_FONT' => 'dejavu sans',

        /**
         * Image DPI setting
         *
         * This setting determines the default DPI setting for images and fonts.  The
         * DPI may be overridden for inline images by explictly setting the
         * image's width & height style attributes (i.e. if the image's native
         * width is 600 pixels and you specify the image's width as 72 points,
         * the image will have a DPI of 600 in the rendered PDF.  The DPI of
         * background images can not be overridden and is controlled entirely
         * via this parameter.
         *
         * For the purposes of DOMPDF, pixels per inch (PPI) = dots per inch (DPI).
         * If a size in html is given as px (or without unit as image size),
         * this tells the corresponding size in pt.
         * This adjusts the relative sizes to be similar to the rendering of the
         * html page in a reference browser.
         *
         * In pdf, always 1 pt = 1/72 inch
         *
         * Rendering resolution of various browsers in px per inch:
         * Windows Firefox and Internet Explorer:
         *   SystemControl->Display properties->FontResolution: Default:96, largefonts:120, custom:?
         * Linux Firefox:
         *   about:config *resolution: Default:96
         *   (xorg screen dimension in mm and Desktop font dpi settings are ignored)
         *
         * Take care about extra font/image zoom factor of browser.
         *
         * In images, <img> size in pixel attribute, img css style, are overriding
         * the real image dimension in px for rendering.
         *
         * @var int
         */
        'DOMPDF_DPI' => 96,

        /**
         * Enable inline PHP
         *
         * If this setting is set to true then DOMPDF will automatically evaluate
         * inline PHP contained within <script type="text/php"> ... </script> tags.
         *
         * Enabling this for documents you do not trust (e.g. arbitrary remote html
         * pages) is a security risk.  Set this option to false if you wish to process
         * untrusted documents.
         *
         * @var bool
         */
        'DOMPDF_ENABLE_PHP' => false,

        /**
         * Enable inline Javascript
         *
         * If this setting is set to true then DOMPDF will automatically insert
         * JavaScript code contained within <script type="text/javascript"> ... </script> tags.
         *
         * @var bool
         */
        'DOMPDF_ENABLE_JAVASCRIPT' => true,

        /**
         * Enable remote file access
         *
         * If this setting is set to true, DOMPDF will access remote sites for
         * images and CSS files as required.
         * This is required for part of test case www/test/image_variants.html through www/examples.php
         *
         * Attention!
         * This can be a security risk, in particular in combination with DOMPDF_ENABLE_PHP and
         * allowing remote access to dompdf.php or on allowing remote html code to be passed to
         * $dompdf = new DOMPDF(, $options); $dompdf->load_html(...);
         * This allows anonymous users to download legally doubtful internet content which on
         * tracing back appears to being downloaded by your server, or allows malicious php code
         * in remote html pages to be executed by your server with your account privileges.
         *
         * @var bool
         */
        'DOMPDF_ENABLE_REMOTE' => true,

        /**
         * A ratio applied to the fonts height to be more like browsers' line height
         */
        'DOMPDF_FONT_HEIGHT_RATIO' => 1.1,

        /**
         * Enable CSS float
         *
         * Allows people to disabled CSS float support
         * @var bool
         */
        'DOMPDF_ENABLE_CSS_FLOAT' => true,

        /**
         * Use the more-than-experimental HTML5 Lib parser
         */
        'DOMPDF_ENABLE_HTML5PARSER' => true,

        /**
         * Enable the built in DOMPDF auto-loader
         *
         * @var bool
         */
        'DOMPDF_ENABLE_AUTOLOAD' => false,

        /**
         * Prepend the DOMPDF autoload function to the SPL autoload functions
         * already registered instead of appending it.
         *
         * @var bool
         */
        'DOMPDF_AUTOLOAD_PREPEND' => false,

        /**
         * Use the built-in font subsetting functionality
         */
        'DOMPDF_ENABLE_FONTSUBSETTING' => true,

        /**
         * Enable support for HTML5 tags
         */
        'DOMPDF_ENABLE_HTML5' => true,

        /**
         * DOMPDF authentication
         * 
         * If you use a font server and you need credentials, you can set them here
         *
         * @var array
         */
        'DOMPDF_AUTHENTICATION' => array(),
    ],
];
