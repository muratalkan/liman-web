<?php
$checkPackage = \App\Controllers\PackageController::verify();
if (!$checkPackage) {
	echo "<script>window.location.href = '".navigate('install')."';</script>";
}
?>

@include('components.tabs', [
    "tabs" => [
        "home" => [
            "icon" => "fas fa-home",
            "view" => "dashboard",
            "onclick" => "getDashboardContent()",
            "notReload" => true,
        ],
        "web_app" => [
            "title" => __('Web Applications'),
            "icon" => "fas fa-globe",
            "view" => "webApp",
            "onclick" => "getWebAppContent()",
            "notReload" => true,
        ],
        "databases" => [
            "title" => __('Databases'),
            "icon" => "fas fa-database",
            "notReload" => true,
            "subTabs" => [
                "db_pgsql" => [
                    "title" => 'PostgreSQL',
                    "icon" => "fas fa-database",
                    "view" => "db_pgsql",
                    "onclick" => "getPgSQLContent()",
                    "notReload" => true,
                ],
                "db_mysql" => [
                    "title" => 'MySQL',
                    "icon" => "fas fa-database",
                    "view" => "db_mysql",
                    "onclick" => "getMySQLContent()",
                    "notReload" => true,
                ]
            ]
        ],
        "php_module" => [
            "title" => __('PHP Modules'),
            "icon" => "fas fa-cubes",
            "view" => "phpModule",
            "onclick" => "getModulesContent()",
            "notReload" => true,
        ]

    ]
])

@include('components.functions')
@include('modals.__modal_loader')


<style>

.modal{
  z-index: 10000000 !important;
}

</style>