/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * @author Kamyar
 */

function modalTosave(heading, html_body, btn_label, id, callback) {
    callback = typeof (callback) != 'undefined' ? callback : function () {
    };
    $('#myModal').remove();
    var frmModal =
            $('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
                    '<div class="modal-dialog">' +
                    '<div class="modal-content">' +
                    '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title" id="myModalLabel">' + heading + '</h4>' +
                    '</div>' +
                    '<div class="modal-body">' + html_body + '</div>' +
                    '<div class="modal-footer">' +
                    '<a id="' + id + '" class="btn btn-success btn-size" data-dismiss="modal" style="">' + btn_label + '</a>' +
                    '<a class="btn btn-default btn-size" data-dismiss="modal" style=""> Cancel </a>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
    frmModal.appendTo('body');
    frmModal.modal('show', callback());
}

function modalAlert(heading, html_body, callback) {
    callback = typeof (callback) != 'undefined' ? callback : function () {
    };
    $('#myAlertModal').remove();
    var frmAlertModal =
            $('<div class="modal fade" id="myAlertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
                    '<div class="modal-dialog">' +
                    '<div class="modal-content">' +
                    '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title" id="myModalLabel">' + heading + '</h4>' +
                    '</div>' +
                    '<div class="modal-body">' + html_body + '</div>' +
                    '<div class="modal-footer">' +
                    '<a id="btn-alert-ok" class="btn btn-success" data-dismiss="modal"> OK </a>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
    frmAlertModal.appendTo('body');
    frmAlertModal.find('#btn-alert-ok').click(function (event) {
        callback();
        frmAlertModal.modal('hide');
        $('#myAlertModal').remove();
    });
    frmAlertModal.modal('hide');
}

function modalTosaveWithInput(heading, html_body, btn_label, id, tmp_val, callback) {
    callback = typeof (callback) != 'undefined' ? callback : function () {
    };
    $('#myModal').remove();
    var frmModal =
            $('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
                    '<input type="hidden" value="' + tmp_val + '" id="hiddenModalInput" />' +
                    '<div class="modal-dialog">' +
                    '<div class="modal-content">' +
                    '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title" id="myModalLabel">' + heading + '</h4>' +
                    '</div>' +
                    '<div class="modal-body">' +
                    '<div>' + html_body + '</div>' +
                    '<br/>' +
                    '<div><input type="text" id="modalInput"></div>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                    '<a id="' + id + '" class="btn btn-success btn-size" data-dismiss="modal" style="">' + btn_label + '</a>' +
                    '<a class="btn btn-default btn-size" data-dismiss="modal" style=""> Cancel </a>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
    frmModal.appendTo('body');
    frmModal.modal('show', callback());
}