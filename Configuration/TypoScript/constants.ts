# Let's define some constants for global configuration
ns_theme_freelancer {	
	website {
		settings {
			#cat = ns_theme_freelancer/website/settings/01; type=string; label=YourLabel
			#myvariable = 
		}
		hfooter {
            #cat = ns_theme_freelancer/website/hfooter/01; type=string; label=Footer Copyright Last Link Text
            linktext =
            #cat = ns_theme_freelancer/website/hfooter/02; type=string; label=Footer Copyright Last Link
            link =
        }
		paths {
			#cat = ns_theme_freelancer/website/settings/01; type=string; label=Template Path
			templateRootPath = typo3conf/ext/ns_theme_freelancer/Resources/Private/Templates/

			#cat = ns_theme_freelancer/website/settings/02; type=string; label=Layouts Path
			layoutRootPath = typo3conf/ext/ns_theme_freelancer/Resources/Private/Layouts/

			#cat = ns_theme_freelancer/website/settings/03; type=string; label=Partials Path
			partialRootPath = typo3conf/ext/ns_theme_freelancer/Resources/Private/Partials/
		}
	}
}
