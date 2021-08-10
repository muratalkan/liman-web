@component('modal-component',[
    "id" => "viewDomainNamesModal"
])

<div id="domainNames-table"></div>

<small class="text-muted">*{{__("To view your websites in the browser, you must add the domain names to '/etc/hosts' file") }}.</small>
<br><small class="text-muted">*{{__("To view your HTTPS websites in the browser, you must to add 'https://' prefix") }}.</small>      

@endcomponent

<script>

    function deleteDomainName(row){
        const webAppName = row.querySelector('#webAppName').innerHTML;
        const domainName = row.querySelector('#domainName').innerHTML;
        
        let form = new FormData();
            form.append("webAppName", webAppName);
            form.append("domainName", domainName);
        createConfirmationAlert(
            domainName,
            '{{ __("Are you sure you want to delete the domain name?") }}',
            form,
            'delete_domain_name',
            'getDomainNames(row)',
            row
        );
    }

</script>