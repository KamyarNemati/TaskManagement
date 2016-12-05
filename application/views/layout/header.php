<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>TaskManagement</title>
        <!-- bootstrap -->
        <link rel="stylesheet" href="web/lib/bootstrap-3.3.7-dist/css/bootstrap.min.css"/>
        <!-- css folder -->
        <link rel="stylesheet" href="web/css/left_menu.css"/>
        <link rel="stylesheet" href="web/css/custom.css"/>
        <link rel="stylesheet" href="web/css/hierarchical_tree_view.css"/>
        <!-- ui-grid -->
        <link rel="styleSheet" href="web/lib/ui-grid-3.1.1/ui-grid.min.css"/>
        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.16/angular.min.js"></script>
        <script src="web/lib/ui-grid-3.1.1/ui-grid.min.js"></script>

        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-touch.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.js"></script>
        <script src="http://ui-grid.info/docs/grunt-scripts/csv.js"></script>
        <script src="http://ui-grid.info/docs/grunt-scripts/pdfmake.js"></script>
        <script src="http://ui-grid.info/docs/grunt-scripts/vfs_fonts.js"></script>
        <script src="web/lib/ui-grid-3.1.1/ui-grid.js"></script>
        <script src="web/lib/ui-grid-3.1.1/ui-grid.css"></script>
    </head>
    <body>
        <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>"/>
        <div class="container">
            <div class="container-fluid">
                <div class="row">
