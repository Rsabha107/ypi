
<div class="offcanvas offcanvas-end custom-offcanvas offcanvas-filter-modal in" id="filterOffcanvas" tabindex="-1"
    aria-labelledby="offcanvasWithBackdropLabel">
    <x-vapp.admin.admin-order-filter-drawer id="" formAction="" formId="filter_order_form" 
        :events="$events" :venues="$venues" :statuses="$statuses" :parkings="$parkings" :vappSizes="$vapp_sizes" :fas="$fas" :variations="$variations" />
</div>