/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * @author Kamyar
 */
function checkTaskTable(callback) {
    $.ajax({
        url: /*base_url +*/ 'api/task/TaskTableCheck',
        type: 'GET',
        async: true,
        data: {
            
        },
        beforeSend: function (xhr) {
        },
        success: function (data, textStatus, jqXHR) {
            callback(data);
        },
        complete: function (jqXHR, textStatus) {
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });
}

/**
 * 
 * @param {type} param
 * @author Kamyar
 */
$(document).ready(function () {
    checkTaskTable(function (obj) {
        if(obj.stat != 0) {
            alert('Unable to verify the table Task.');
        }
    });
});

/**
 * 
 * @param {type} param1
 * @param {type} param2
 * @param {type} param3
 * @author Kamyar
 */
$(document).on('click', '#btn_save', function(e) {
    e.preventDefault();
});

/**
 * 
 * @param {type} callback
 * @returns {undefined}
 * @author Kamyar
 * @description Hierarchical Visual
 * 
 * Begin: code
 */
function createCstHierarchyView(callback) {
    $.ajax({
        url: /*base_url +*/ 'api/task/HierarchicalVisual',
        type: 'GET',
        async: true,
        data: {
            
        },
        beforeSend: function (xhr) {
        },
        success: function (data, textStatus, jqXHR) {
            callback(data.view);
        },
        complete: function (jqXHR, textStatus) {
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });
}
function drawHierarchy() {
    createCstHierarchyView(function (view) {
        $('#hFrame').empty();
        $('#hFrame').append(view);
    });
}
/*
 * Kamyar
 * End: code
 * Description: Hierarchical Visual
 */

/**
 * 
 * @param {type} callback
 * @returns {undefined}
 * @author Kamyar
 */
function saveTask(callback) {
    $.ajax({
        url: /*base_url +*/ 'api/task/SaveTask',
        type: 'POST',
        async: true,
        data: {
            title: $('#taskName').val(),
            parent_id: $('#taskParent').val()
        },
        beforeSend: function (xhr) {
        },
        success: function (data, textStatus, jqXHR) {
            callback(data);
        },
        complete: function (jqXHR, textStatus) {
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });
}

/**
 * 
 * @param {type} id
 * @param {type} callback
 * @returns {undefined}
 * @author Kamyar
 */
function changeStatus(id, callback) {
    $.ajax({
        url: /*base_url +*/ 'api/task/ChangeStatus',
        type: 'POST',
        async: true,
        data: {
            id: id
        },
        beforeSend: function (xhr) {
        },
        success: function (data, textStatus, jqXHR) {
            callback(data);
        },
        complete: function (jqXHR, textStatus) {
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });
}

/**
 * 
 * @param {type} id
 * @param {type} name
 * @param {type} callback
 * @returns {undefined}
 * @author Kamyar
 */
function changeName(id, name, callback) {
    $.ajax({
        url: /*base_url +*/ 'api/task/ChangeName',
        type: 'POST',
        async: true,
        data: {
            id: id,
            name: name
        },
        beforeSend: function (xhr) {
        },
        success: function (data, textStatus, jqXHR) {
            callback(data);
        },
        complete: function (jqXHR, textStatus) {
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });
}

function changeParent(id, parent_id, callback) {
    $.ajax({
        url: /*base_url +*/ 'api/task/ChangeParent',
        type: 'POST',
        async: true,
        data: {
            id: id,
            parent_id: parent_id
        },
        beforeSend: function (xhr) {
        },
        success: function (data, textStatus, jqXHR) {
            callback(data);
        },
        complete: function (jqXHR, textStatus) {
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });
}