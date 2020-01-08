# TypoScript for rendering in frontend
tt_content.gridelements_pi1.20.10.setup >
tt_content.gridelements_pi1.20.10.setup {

    nsBase1Col < lib.gridelements.defaultGridSetup
    nsBase1Col {
        cObject = FLUIDTEMPLATE
        cObject {
            file = typo3conf/ext/ns_theme_freelancer/Resources/Private/Extensions/Grid/OneColumn.html
        }
    }    

    nsBase3Col < lib.gridelements.defaultGridSetup
    nsBase3Col {
        cObject = FLUIDTEMPLATE
        cObject {
            file = typo3conf/ext/ns_theme_freelancer/Resources/Private/Extensions/Grid/ThreeColumn.html
        }
    } 

    nsFree3Col < lib.gridelements.defaultGridSetup
    nsFree3Col {
        cObject = FLUIDTEMPLATE
        cObject {
            file = typo3conf/ext/ns_theme_freelancer/Resources/Private/Extensions/Grid/FooterContent.html
        }
    } 

}
