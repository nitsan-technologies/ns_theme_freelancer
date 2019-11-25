# TypoScript for rendering in frontend
tt_content.gridelements_pi1.20.10.setup >
tt_content.gridelements_pi1.20.10.setup {

    4 < lib.gridelements.defaultGridSetup
    4 {
        cObject = FLUIDTEMPLATE
        cObject {
            file = typo3conf/ext/ns_theme_freelancer/Resources/Private/Extensions/Grid/OneColumn.html
        }
    }    

 2 < lib.gridelements.defaultGridSetup
    2 {
        cObject = FLUIDTEMPLATE
        cObject {
            file = typo3conf/ext/ns_theme_freelancer/Resources/Private/Extensions/Grid/ThreeColumn.html
        }
    } 

 1 < lib.gridelements.defaultGridSetup
    1 {
        cObject = FLUIDTEMPLATE
        cObject {
            file = typo3conf/ext/ns_theme_freelancer/Resources/Private/Extensions/Grid/FooterContent.html
        }
    } 

}
