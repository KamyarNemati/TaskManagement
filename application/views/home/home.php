<div class="col-md-9">
    <div class="row">
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <ul class="breadcrumb">
                    <li class="active">Task List</li>
                </ul>
            </div>
            <div class="block-content collapse in">
                <div class="col-md-12">
                    <div class="table-toolbar">
                        <div class="row">

                        </div>
                    </div>
                    <!-- Content -->
                    <div class="row">
                        <div class="col-md-12">
                            <form id="taskForm">
                                <fieldset>
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-md-2">Task Name:&nbsp;</label>
                                            <input type="text" id="taskName" name="taskName" class="col-md-10"/>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-md-2">Parent Task:&nbsp;</label>
                                            <select class="col-md-10" id="taskParent">
                                            </select>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row custom-style-right">
                                        <button id="btn_save" class="btn btn-success">Save</button>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                    <br/>
                    <div class="row" ng-app="app">
                        <div ng-controller="MainCtrl">
                            <div id="grid1" ui-grid="gridOptions" ui-grid-pagination ui-grid-pinning class="grid"></div>
                        </div>
                    </div>
                    <br/>
                    <div class="row custom-style-right">
                        <button id="btn_refresh" class="btn btn-success">Refresh</button>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12 tree" id="hFrame">
                            
                        </div>
                    </div>
                    <!-- End Content -->
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var app = angular.module('app', ['ngTouch', 'ui.grid', 'ui.grid.pagination', 'ui.grid.resizeColumns', 'ui.grid.pinning']);

    app.controller('MainCtrl', ['$scope', '$http', 'uiGridConstants', function ($scope) {

            $(document).on('click', '#btnConfirm', function () {
                saveTask(function(obj) {
                    if(obj.stat == 0) {
                        modalAlert('Save', 'Saved successfully.');
                    } else {
                        modalAlert('Error', obj.msg);
                    }
                    getPage();
                });
            });

            $(document).on('click', '#btn_save', function () {
                var task_name = $('#taskName').val();
                if(task_name === '') {
                    modalTosave('Save', 'Please pick a name.', 'Oops :p', '', function () {});
                } else {
                    modalTosave('Save', 'Are you sure?', 'Yes I am :-)', 'btnConfirm', function () {});
                }
            });
            
            $(document).on('click', '#btn_refresh', function () {
                getPage();
            });
            
            $scope.clickChangeStatus = function(row) {
                var id = row.entity.id;
                changeStatus(id, function(obj) {
                    if(obj.stat == 0) {
                        getPage();
                    } else {
                        modalTosave('Error', obj.msg, 'OK', '', function() {});
                    }
                });
            };
            
            $(document).on('click', '#btnConfirmRename', function () {
                var id = $('#hiddenModalInput').val();
                var name = $('#modalInput').val();
                if(name !== '') {
                    changeName(id, name, function(obj) {
                        if(obj.stat == 0) {
                            getPage();
                        } else {
                            modalTosave('Error', obj.msg, 'OK', '', function() {});
                        }
                    });
                }
            });
            
            $scope.clickRename = function(row) {
                var id = row.entity.id;
                modalTosaveWithInput('Rename', 'What name is in your mind?', 'Rename', 'btnConfirmRename', id, function() {});
            };
            
            $(document).on('click', '#btnConfirmChangeParent', function () {
                var id = $('#hiddenModalInput').val();
                var parent_id = $('#modalInput').val();
                if(parent_id !== '') {
                    changeParent(id, parent_id, function(obj) {
                        if(obj.stat == 0) {
                            getPage();
                        } else {
                            modalTosave('Error', obj.msg, 'OK', '', function() {});
                        }
                    });
                }
            });
            
            $scope.clickChangeParent = function(row) {
                var id = row.entity.id;
                modalTosaveWithInput('Change Parent', 'Select the parent task.', 'Rename', 'btnConfirmChangeParent', id, function() {});
            };

            /*
             * Kamyar:
             * AngularJS parameters
             */
            var paginationOptions = {
                pageNumber: 1,
                pageSize: 20,
                sort_field: 'id',
                order: 1,
                filter: {}
            };

            $scope.gridOptions = {
                enableFiltering: true,
                paginationPageSizes: [10, 20, 50],
                paginationPageSize: 20,
                useExternalPagination: true,
                useExternalSorting: true,
                useExternalFiltering: true,
                enableColumnResizing: true,
                columnDefs: [
                    {name: 'ID', field: 'id'},
                    {name: 'Title', field: 'title'},
                    {name: 'Status', field: 'status'},
                    {name: 'Parent', field: 'parent_id'},
                    {name: 'Action', headerCellTemplate: '<div style="text-align: center;font-size:13px !important;margin-top:7px !important; color: #000 !important;">Action</div>', enableFiltering: false, field: 'id', cellTemplate: '<div style="text-align: center; padding-top:5px;"><a style="cursor:pointer;" ng-click="grid.appScope.clickChangeStatus(row)">Toggle status</a>&nbsp;|&nbsp;<a style="cursor:pointer;" ng-click="grid.appScope.clickRename(row)">Rename</a>&nbsp;|&nbsp;<a style="cursor:pointer;" ng-click="grid.appScope.clickChangeParent(row)">ChangeParent</a></div>', width: 250}
                ],
                onRegisterApi: function (gridApi) {
                    $scope.gridApi = gridApi;
                    $scope.gridApi.core.on.sortChanged($scope, function (grid, sortColumns) {
                        if (sortColumns.length == 0) {
                            paginationOptions.order = 1;
                            paginationOptions.sort_field = sortColumns[0].colDef.field;
                        } else {
                            paginationOptions.sort_field = sortColumns[0].colDef.field;
                            if (sortColumns[0].sort.direction === 'asc') {
                                paginationOptions.order = 1;
                            } else {
                                paginationOptions.order = 2;
                            }
                            //paginationOptions.order = sortColumns[0].sort.direction;
                        }
                        getPage();
                    });
                    gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                        paginationOptions.pageNumber = newPage;
                        paginationOptions.pageSize = pageSize;
                        getPage();
                    });

                    /*
                     * Kamyar:
                     * To support external filtering
                     */
                    $scope.gridApi.core.on.filterChanged($scope, function () {
                        paginationOptions.pageNumber = 1;
                        var is_true = true;
                        var grid = this.grid;
                        var filter = {};
                        var id = '';
                        var title = '';
                        var status = '';
                        var parent_id = '';
                        if (grid !== '') {
                            if (grid.columns[0].filters[0].term !== undefined) {
                                id = grid.columns[0].filters[0].term;
                            }
                            if (grid.columns[1].filters[0].term !== undefined) {
                                title = grid.columns[1].filters[0].term;
                            }
                            if (grid.columns[2].filters[0].term !== undefined) {
                                status = grid.columns[2].filters[0].term;
                            }
                            if (grid.columns[3].filters[0].term !== undefined) {
                                parent_id = grid.columns[3].filters[0].term;
                            }
                        }
                        if (is_true) {
                            filter = {
                                id: id,
                                title: title,
                                status: status,
                                parent_id: parent_id
                            };
                            paginationOptions.filter = filter;
                            getPage();
                        }
                    });
                }
            };

            $scope.clickArchiveMember = function (row) {
                alert(JSON.stringify(row));
            };
            var getPage = function () {
                var start = (paginationOptions.pageSize * paginationOptions.pageNumber) - paginationOptions.pageSize + 1;
                var length = paginationOptions.pageSize;
                var order = paginationOptions.order;
                var sort_field = paginationOptions.sort_field;
                var filter = paginationOptions.filter;
                var params = {
                    length: length,
                    start: start,
                    order: order,
                    sort_field: sort_field,
                    filter: filter
                };
                $scope.loading = true;
                $.ajax({
                    url: /*base_url +*/ "api/task/GetTasks",
                    type: "GET",
                    data: {
                        params: JSON.stringify(params)
                    },
                    beforeSend: function (xhr) {

                    },
                    success: function (data, textStatus, jqXHR) {
                        $scope.loading = false;
                        if (data.stat != 0) {
                            alert(data.errmsg);
                            return false;
                        }
                        $scope.gridOptions.totalItems = data.count;
                        $scope.gridOptions.data = data.data;
                        $scope.gridApi.core.refresh();
                        /*
                         * Update the parent list
                         */
                        var opt = [];
                        opt.push('<option value="0">No Parent</option>');
                        if (data.data.length > 0) {
                            for (var i = 0; i < data.data.length; ++i) {
                                opt.push('<option value="' + data.data[i].id + '">' + data.data[i].id + '&nbsp;&nbsp;&nbsp;' + data.data[i].title + '</option>');
                            }
                        }
                        $("#taskParent").html(opt.join(''));
                        drawHierarchy();
                    },
                    complete: function (jqXHR, textStatus) {

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $scope.loading = false;
                    }
                });
            };
            getPage();
        }]);
</script>