# Grab all the constant
plugin {
    ns_theme_callcenter {
        settings {
            // Your constant

        }
    }
}

// Initiate Page Object
page = PAGE
page {
    // Setup favion
    shortcutIcon = typo3conf/ext/ns_theme_freelancer/Resources/Public/Icons/favicon.ico

    // Set viewport
    meta {
        viewport = width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no
    }

	headerData{
		10 = TEXT
		10.value(			
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="">
            <meta name="author" content="">         
            <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
            <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
            <script>
             (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
             (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
             m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
             })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
             ga('create', '{$ns_theme_freelancer.website.settings.googleanalytics}', 'auto');
             ga('send', 'pageview');
           </script>
		)
	}

    // Initiate all the css-together
    includeCSS {
        //20 = 
        
        80 = typo3conf/ext/ns_theme_freelancer/Resources/Public/css/freelancer.min.css
        90 = typo3conf/ext/ns_theme_freelancer/Resources/Public/css/custom.css
	    100 = typo3conf/ext/ns_theme_freelancer/Resources/Public/vendor/fontawesome-free/css/all.min.css
    }

    // Initiate all the js-together
    includeJSFooter {
        //50 = typo3conf/ext/ns_theme_freelancer/Resources/Public/js/clean-blog.min.js
	20 = typo3conf/ext/ns_theme_freelancer/Resources/Public/vendor/jquery/jquery.min.js
	30 = typo3conf/ext/ns_theme_freelancer/Resources/Public/vendor/bootstrap/js/bootstrap.bundle.min.js
	40 = typo3conf/ext/ns_theme_freelancer/Resources/Public/vendor/jquery-easing/jquery.easing.min.js
	50 = typo3conf/ext/ns_theme_freelancer/Resources/Public/js/jqBootstrapValidation.js
	60 = typo3conf/ext/ns_theme_freelancer/Resources/Public/js/contact_me.js
	70 = typo3conf/ext/ns_theme_freelancer/Resources/Public/js/freelancer.min.js
    }
    
    10 = FLUIDTEMPLATE
    10 {
        layoutRootPath = {$ns_theme_freelancer.website.paths.layoutRootPath}
        partialRootPath = {$ns_theme_freelancer.website.paths.partialRootPath}
        templateRootPath = {$ns_theme_freelancer.website.paths.templateRootPath}

        // Let's automatically choose backend layout and template
        file.stdWrap.cObject = CASE
        file.stdWrap.cObject {
            key.data = levelfield:-1, backend_layout_next_level, slide
            key.override.field = backend_layout
            
            default = TEXT
            default.value = {$ns_theme_freelancer.website.paths.templateRootPath}Default.html

            pagets__content = TEXT
            pagets__content.value = {$ns_theme_freelancer.website.paths.templateRootPath}FullWIdth.html
        }
        settings < plugin.ns_basetheme.settings
        settings.childSettings < plugin.ns_theme_freelancer.settings
    }
}

# Get default content
lib {
    headerContent < lib.content
    headerContent.select.where = colPos = 0
 mainContent < lib.content
mainContent.select.where = colPos = 0

    footerContent < lib.content
	footerContent.select.pidInList = 9
    footerContent.select.where = colPos = 0
    footerContent.slide = -1
}

# Set copyright
lib.copyright >
lib.copyright = COA
lib.copyright {
    stdWrap.wrap = |

    1 = TEXT
    1.current = 1
    1.strftime = %Y
    1.wrap = &copy;&nbsp;|&nbsp;

    2 = TEXT
    2.value = {$ns_basetheme.website.settings.copyright}
    2.wrap = |
}

lib.PageContent = COA
lib.PageContent{
	10 = CONTENT
	10{
		table = pages
		select{
			pidInList = {$ns_basetheme.website.settings.main_menu}
			orderBy = sorting
			where = title != ''

		}
		renderObj = COA
		renderObj{
			20 = CONTENT
			20{
				table = tt_content
				select{
					pidInList.field = uid
					orderBy = sorting
					where = colPos = 0
				}
                stdWrap.wrap = <div id="{field:subtitle}" class="section mcb-section">|</div>
                stdWrap.wrap.insertData = 1
				# renderObj = COA
	   #  		renderObj {
				# 	30 < tt_content
				# }				
			}
			wrap = |
		}	
	}	
}

# <body> class based on backend_layout
page.bodyTagCObject {
    wrap = <body id="page-top" class="|">

    10 = COA
    10 {
        # layout
        50 = CASE
        50 {
            key.data = levelfield:-1, backend_layout_next_level, slide
            key.override.field = backend_layout

            default = TEXT
            default.value = default

            pagets__content = TEXT
            pagets__content.value = content
        }
    }
}
