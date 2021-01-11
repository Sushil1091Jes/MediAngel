/* ----------------------------------------------------------------------------
 * MediAngel - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

(function () {

    'use strict';

    /**
     * CategoriesHelper Class
     *
     * This class contains the core method implementations that belong to the categories tab
     * of the backend services page.
     *
     * @class CategoriesHelper
     */
    function CategoriesHelper() {
        this.filterResults = {};
        this.filterCategoriesTemplateResults = {};
        this.filterLimit = 20;
    }

    /**
     * Binds the default event handlers of the categories tab.
     */
    CategoriesHelper.prototype.bindEventHandlers = function () {
        var instance = this;

        /**
         * Event: Filter Categories Cancel Button "Click"
         */
        $('#filter-categories .clear').on('click', function () {
            $('#filter-categories .key').val('');
            instance.filter('');
            instance.resetForm();
           
        });

        /**
         * Event: Filter Categories Form "Submit"
         */
        $('#filter-categories form').submit(function () {
            var key = $('#filter-categories .key').val();
            $('.selected').removeClass('selected');
            instance.resetForm();
            instance.filter(key);
            return false;
        });

        /**
         * Event: Filter Categories Row "Click"
         *
         * Displays the selected row data on the right side of the page.
         */
        $(document).on('click', '.category-row', function () {
            if ($('#filter-categories .filter').prop('disabled')) {
                $('#filter-categories .results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            var categoryId = $(this).attr('data-id');

            var category = instance.filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(categoryId);
            });

            instance.display(category);
            $('#filter-categories .selected').removeClass('selected');
            $(this).addClass('selected');
            $('#edit-category, #delete-category').prop('disabled', false);
        });

        /**
         * Event: Add Category Button "Click"
         */
        $('#add-category').on('click', function () {
            instance.resetForm();
            $('#categories .add-edit-delete-group').hide();
            $('#categories .save-cancel-group').show();
            $('#categories .record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-categories button').prop('disabled', true);
            $('#filter-categories .results').css('color', '#AAA');
            $('.record-details button').prop('disabled', false);
        });

        /**
         * Event: Edit Category Button "Click"
         */
        $('#edit-category').on('click', function () {
            $('#categories .add-edit-delete-group').hide();
            $('#categories .save-cancel-group').show();
            $('#categories .record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-categories button').prop('disabled', true);
            $('#filter-categories .results').css('color', '#AAA');
            $('.record-details button').prop('disabled', false);
        });

        /**
         * Event: Delete Category Button "Click"
         */
        $('#delete-category').on('click', function () {
            var categoryId = $('#category-id').val();

            var buttons = [
                {
                    text: EALang.cancel,
                    click: function () {
                        $('#message-box').dialog('close');
                    }
                },
                {
                    text: EALang.delete,
                    click: function () {
                        instance.delete(categoryId);
                        $('#message-box').dialog('close');
                    }
                },
            ];

            GeneralFunctions.displayMessageBox(EALang.delete_category,
                EALang.delete_record_prompt, buttons);
        });

        /**
         * Event: Categories Save Button "Click"
         */
        $('#save-category').on('click', function () {
             if (!instance.validate()) {
                return;
            }
            var category = {
                name: $('#category-name').val(),
                description: $('#category-description').val(),
                image: $('#category-image').val()
            };

            if ($('#category-id').val() !== '') {
                category.id = $('#category-id').val();
            }

            instance.uploadFileAndSave(category);
           
        });

        /**
         * Event: Cancel Category Button "Click"
         */
        $('#cancel-category').on('click', function () {
            var id = $('#category-id').val();
            instance.resetForm();
            if (id !== '') {
                instance.select(id, true);
            }
        });

       /**
        * Event: Show category template  "Click"
        *
        */
       $('#insert-categoriestemplate').on('click', function () {
            instance.categoriesTemplateGetHtml();
            var $dialog = $('#manage-categoriestemplate');
            $dialog.modal('show');
           
        });

        /**
         * Event: Confirm category template "Click"
         *
         */
        $('#confirm-categoriestemplate').on('click', function () {
            var $checked = $("#manage-categoriestemplate").find('input[type=checkbox]:checked');
            if ($checked.length > 0) {
                var categoriesTemplate_id = $checked.parents("tr").eq(0).attr("data-id");
                var categoriesTemplate = instance.filterCategoriesTemplateResults.find(function (filterResult) {
                    return Number(filterResult.id) === Number(categoriesTemplate_id);
                });
                $('#category-name').val(categoriesTemplate.name);
                $('#category-image').val(categoriesTemplate.image);
                $('#uploaded_image').attr("src", categoriesTemplate.image);
            }
            var $dialog = $('#manage-categoriestemplate');
            // Close the modal dialog 
            $dialog.find('.alert').addClass('d-none');
            $dialog.modal('hide');
        });

       /**
        * Event: Select Categories  Template  "Click"
        *
        */
        $("#manage-categoriestemplate").on('click', '.categories_template_row', function () {
            /*������������ʱ,�ѵ�ѡ��ť��Ϊѡ��״̬*/
            if (event.target.type != 'checkbox') {
                /*������������ʱ,�Ѹ�ѡ����Ϊѡ��״̬*/
                if ($(this).find("input[type='checkbox']").prop("checked")) {

                    $(this).find("input[type='checkbox']").prop("checked", false);
                } else {
                    $(this).find("input[type='checkbox']").prop("checked", true);
                }
                $(this).find("input[type='checkbox']").trigger('change'); // ����change�¼�
            }
            var categorytemplate_id = $(this).attr('data-id');
            var chks = $("input[name='categoryTemplateCheckbox']");
            for (var i = 0; i < chks.length; i++) {
                var tr_Id = $(chks[i]).parents("tr").attr('data-id');
                if (categorytemplate_id == tr_Id) {
                    continue;
                }
                if ($(chks[i]).prop("checked")) {
                    $(chks[i]).prop("checked", false);
                }
            }
        });


    };

    /**
     * Filter service categories records.
     *
     * @param {String} key This key string is used to filter the category records.
     * @param {Number} selectId Optional, if set then after the filter operation the record with the given
     * ID will be selected (but not displayed).
     * @param {Boolean} display Optional (false), if true then the selected record will be displayed on the form.
     */
    CategoriesHelper.prototype.filter = function (key, selectId, display) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_service_categories';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            key: key,
            limit: this.filterLimit
        };

        $.post(url, data)
            .done(function (response) {
                this.filterResults = response;

                $('#filter-categories .results').empty();

                response.forEach(function(category) {
                    $('#filter-categories .results')
                        .append(this.getFilterHtml(category))
                        .append($('<hr/>'));
                }.bind(this));

                if (response.length === 0) {
                    $('#filter-categories .results').append(
                        $('<em/>', {
                            'text': EALang.no_records_found
                        })
                    );
                } else if (response.length === this.filterLimit) {
                    $('<button/>', {
                        'type': 'button',
                        'class': 'btn btn-block btn-outline-secondary load-more text-center',
                        'text': EALang.load_more,
                        'click': function () {
                            this.filterLimit += 20;
                            this.filter(key, selectId, display);
                        }.bind(this)
                    })
                        .appendTo('#filter-categories .results');
                }

                if (selectId) {
                    this.select(selectId, display);
                }
            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Save a category record to the database (via AJAX post).
     *
     * @param {Object} category Contains the category data.
     */
    CategoriesHelper.prototype.save = function (category) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_service_category';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            category: JSON.stringify(category)
        };

        $.post(url, data)
            .done(function (response) {
                Backend.displayNotification(EALang.service_category_saved);
                this.resetForm();
                $('#filter-categories .key').val('');
                this.filter('', response.id, true);
                BackendServices.updateAvailableCategories();
            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Delete category record.
     *
     * @param Number} id Record ID to be deleted.
     */
    CategoriesHelper.prototype.delete = function (id) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_service_category';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            category_id: id
        };

        $.post(url, data)
            .done(function () {
                Backend.displayNotification(EALang.service_category_deleted);

                this.resetForm();
                this.filter($('#filter-categories .key').val());
                BackendServices.updateAvailableCategories();
            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Display a category record on the form.
     *
     * @param {Object} category Contains the category data.
     */
    CategoriesHelper.prototype.display = function (category) {
        $('#category-id').val(category.id);
        $('#category-name').val(category.name);
        $('#category-description').val(category.description);
        $('#category-image').val('');
        $('#uploaded_image').attr("src", './../../assets/img/user.png');
        if (category.image) {
            $('#category-image').val(category.image);
            $('#uploaded_image').attr("src", category.image);
        }
    };

    /**
     * Validate category data before save (insert or update).
     *
     * @return {Boolean} Returns the validation result.
     */
    CategoriesHelper.prototype.validate = function () {
        $('#categories .has-error').removeClass('has-error');
        $('#categories .form-message')
            .removeClass('alert-danger')
            .hide();

        try {
            var missingRequired = false;

            // Check if imagr 
            if ($('#upload_image')[0].files[0] == undefined && ($('#category-image').val() === '' || $('#category-image').val() === null)) {
                throw new Error(EALang.image_upload);
            }

            $('#categories .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(EALang.fields_are_required);
            }

            return true;
        } catch (error) {
            $('#categories .form-message')
                .addClass('alert-danger')
                .text(error.message)
                .show();
            return false;
        }
    };

    /**
     * Bring the category form back to its initial state.
     */
    CategoriesHelper.prototype.resetForm = function () {
        $('#filter-categories .selected').removeClass('selected');
        $('#filter-categories button').prop('disabled', false);
        $('#filter-categories .results').css('color', '');

        $('#categories .add-edit-delete-group').show();
        $('#categories .save-cancel-group').hide();
        $('#categories .record-details')
            .find('input, select, textarea')
            .val('')
            .prop('disabled', true);
        $('#edit-category, #delete-category').prop('disabled', true);

        $('#categories .record-details .has-error').removeClass('has-error');
        $('#categories .record-details .form-message').hide();

        $('#uploaded_image').attr("src", './../../assets/img/user.png');

        $('#insert-categoriestemplate').prop('disabled', true);
     
    };

    /**
     * Get the filter results row HTML code.
     *
     * @param {Object} category Contains the category data.
     *
     * @return {String} Returns the record HTML code.
     */
    CategoriesHelper.prototype.getFilterHtml = function (category) {
        return $('<div/>', {
            'class': 'category-row entry',
            'data-id': category.id,
            'html': [
                $('<strong/>', {
                    'text': category.name
                }),
                $('<br/>'),
            ]
        });
    };

    /**
     * Select a specific record from the current filter results.
     *
     * If the category ID does not exist in the list then no record will be selected.
     *
     * @param {Number} id The record ID to be selected from the filter results.
     * @param {Boolean} display Optional (false), if true then the method will display the record
     * on the form.
     */
    CategoriesHelper.prototype.select = function (id, display) {
        display = display || false;

        $('#filter-categories .selected').removeClass('selected');

        $('#filter-categories .category-row[data-id="' + id + '"]').addClass('selected');

        if (display) {
            var category = this.filterResults.find(function (category) {
                return Number(category.id) === Number(id);
            }.bind(this));

            this.display(category);

            $('#edit-category, #delete-category').prop('disabled', false);
        }
    };

        /**
      * Save a category record to the database (via AJAX post).
      *
      * @param {Object} category Contains the category data.
      */
    CategoriesHelper.prototype.uploadFileAndSave = function (category) {
        var url = GlobalVariables.baseUrl + '/application/upload/upload_fileBase64.php';
        var formData = new FormData();
        if ($('#upload_image')[0].files[0] == undefined) {
            this.save(category);
            return;
        }
        var that = this;
        formData.append('upload_image', $("#uploaded_image").attr("src"));
        try {
            /*ajax�ύ*/
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                async: true,
                contentType: false, //����
                processData: false, //����
                dataType: "json",
                success: function (data) {
                    if (data.file == "") {
                        throw new Error(EALang.image_upload_error);
                    } else {
                        $("#category-image").val(data.file);
                        $('#upload_image').val('');
                        category.image = data.file;
                        that.save(category);
                    }
                }
            });
        } catch (error) {
            $('#providers .form-message')
                .addClass('alert-danger')
                .text(error.message)
                .show();
        }
    };

    /**
    * Filter service categories template records.
    *
    * @param {String} key This key string is used to filter the category  template records.
    * @param {Number} selectId Optional, if set then after the filter operation the record with the given
    * ID will be selected (but not displayed).
    * @param {Boolean} display Optional (false), if true then the selected record will be displayed on the form.
    */
    CategoriesHelper.prototype.filterCategoriesTemplate = function (key, selectId, display) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_service_categories_template';
        var data = {
            csrfToken: GlobalVariables.csrfToken,
            key: key,
            limit: this.filterLimit
        };
        $("#insert-categoriestemplate").hide();
        $.post(url, data)
            .done(function (response) {
                this.filterCategoriesTemplateResults = response;
                if (response.length >0) {
                    $("#insert-categoriestemplate").show();
                }
            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
    * Filter service categories template records.
    *
    * @param {String} key This key string is used to filter the category  template records.
    * @param {Number} selectId Optional, if set then after the filter operation the record with the given
    * ID will be selected (but not displayed).
    * @param {Boolean} display Optional (false), if true then the selected record will be displayed on the form.
    */
    CategoriesHelper.prototype.categoriesTemplateGetHtml = function () {
        $('#manage-categoriestemplate .modal-body').find("table").empty();
        var tableHtml = "<tr><th style='width:15%'><div></div></th><th><div>" + EALang.name + "</div></th><th><div>" + EALang.image + "</div></th> </tr >";
        var response = this.filterCategoriesTemplateResults;
        if (response.length === 0) {
            tableHtml += "<tr><td colspan='3'><div>" + EALang.no_records_found + "</div></td></tr >";
        } else {
            response.forEach(function (categoryTemplate) {
                tableHtml += "<tr class='categories_template_row' data-id='" + categoryTemplate.id + "' >";
                tableHtml += "<td><input type='checkbox' name='categoryTemplateCheckbox'></input></td >";
                tableHtml += "<td> <div>" + categoryTemplate.name + "</div></td >";
                tableHtml += "<td  align='center' valign='middle'> <div><img  src='" + categoryTemplate.image + "' class='img - responsive' style='width: 100px; height: 100px;'></div></td >";
                tableHtml += "</tr>";
            });
        }
        $('#manage-categoriestemplate .modal-body').find("table").html(tableHtml);
    };

    window.CategoriesHelper = CategoriesHelper;
})();
