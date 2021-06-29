@include('modals.dashboardTab.viewService')

@include('modals.webAppTab.addWebApp')
@include('modals.webAppTab.addDomainName')
@include('modals.webAppTab.createFtpUser')
@include('modals.webAppTab.viewDomainNames')
@include('modals.webAppTab.viewFtpUsers')

@include('modals.databaseTab.mysql.mysql_createDB')
@include('modals.databaseTab.mysql.mysql_createUser')
@include('modals.databaseTab.mysql.mysql_grantPrivilege')
@include('modals.databaseTab.mysql.mysql_viewUserDB')
@include('modals.databaseTab.mysql.mysql_viewDBTable')

@include('modals.databaseTab.pgsql.pgsql_createDB')
@include('modals.databaseTab.pgsql.pgsql_createUser')
@include('modals.databaseTab.pgsql.pgsql_grantPrivilege')
@include('modals.databaseTab.pgsql.pgsql_viewUserDB')
@include('modals.databaseTab.pgsql.pgsql_viewDBTable')

@include('modals.moduleTab.installModule')


<script>

    function hideModal(modalID){
        $('#'+modalID).modal('hide');
    }

    function initializeModalFunctions(){
        initializeWebAppModal();
        initializePgSQLPrivilegeModal();
    }

    $('.modal').on('hidden.bs.modal', function(e){
        if (typeof $(this).find('form')[0] !== 'undefined'){
            $(this).find(".alert").fadeOut();
            $(this).find('form')[0].reset();
            initializeModalFunctions();
        }
    });

    function changeModalTitle(modalID, title){
        $('#'+modalID).find('.modal-header').html(`
                    ${title}
            <button type="button" class="close" aria-label="Close" onclick="closeCurrentModal('${modalID}')">
                <span aria-hidden="true">&times;</span>
            </button>
        `);
    }

</script>