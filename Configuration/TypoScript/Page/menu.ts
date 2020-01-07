// Draw main menu
menu.main = HMENU
menu.main {
	
	special = directory
	special.value = {$ns_basetheme.website.settings.main_menu}
		
	1 = TMENU
	1 {
		wrap = <ul id="menu-main-menu" class="navbar-nav ml-auto">|</ul>
		expAll = 1
		noBlur = 1
		
		NO = 1
		NO {
			doNotLinkIt = 1
			ATagTitle {
				field = title
				fieldRequired = nav_title
			}
			ATagParams = 
			
			wrapItemAndSub.insertData = 1
			wrapItemAndSub = <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#{field:subtitle}">|</a></li>
			stdWrap.htmlSpecialChars = 1
		}

		ACT < .NO
        ACT {
            wrapItemAndSub = <li class="nav-item mx-0 mx-lg-1 active">|</li>
        }
	}
}
