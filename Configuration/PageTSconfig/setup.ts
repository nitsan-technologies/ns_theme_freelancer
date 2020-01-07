# Include the BackendLayouts
<INCLUDE_TYPOSCRIPT: source="DIR:EXT:ns_theme_freelancer/Configuration/PageTSconfig/BackendLayouts" extensions="ts">

# Remove default custom elements from EXT:ns_basetheme 
mod.wizards.newContentElement.wizardItems.extra {
  show := removeFromList(ns_imageteaser, ns_record, ns_slider)
}
tx_gridelements.setup {
    4 < .nsBase1Col
    4.title = Container Grid
    nsBase1Col >
    3 < .nsBase4Col
    nsBase4Col >
    2 < .nsBase3Col
    nsBase3Col >
    nsBase2Col >
}