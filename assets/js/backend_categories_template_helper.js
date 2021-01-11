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
     * CategoriesTemplateHelper Class
     *
     * This class contains the core method implementations that belong to the categories tab
     * of the backend services page.
     *
     * @class CategoriesTemplateHelper
     */
    function CategoriesTemplateHelper() {
        this.filterResults = {};
        this.filterLimit = 20;
    }

    /**
     * Binds the default event handlers of the categories tab.
     */
    CategoriesTemplateHelper.prototype.bindEventHandlers = function () {
        var instance = this;

        /**
         * Event: Filter Categories Cancel Button "Click"
         */
        $('#filter-categoriesTemplate .clear').on('click', function () {
            $('#filter-categoriesTemplate .key').val('');
            instance.filter('');
            instance.resetForm();
        });

        /**
         * Event: Filter Categories Form "Submit"
         */
        $('#filter-categoriesTemplate form').submit(function () {
            var key = $('#filter-categoriesTemplate .key').val();
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
        $(document).on('click', '.categoryTemplate-row', function () {
            if ($('#filter-categoriesTemplate .filter').prop('disabled')) {
                $('#filter-categoriesTemplate .results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            $("#categories_template").find(".record-details").show();

            var categoriesTemplateId = $(this).attr('data-id');

            var categoriesTemplate = instance.filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(categoriesTemplateId);
            });

            instance.display(categoriesTemplate);
            $('#filter-categoriesTemplate .selected').removeClass('selected');
            $(this).addClass('selected');
            $('#edit-categoryTemplate, #delete-categoryTemplate').prop('disabled', false);
        });

        /**
         * Event: Add Category Button "Click"
         */
        $('#add-categoryTemplate').on('click', function () {
            instance.resetForm();
            $('#categories_template .add-edit-delete-group').hide();
            $('#categories_template .save-cancel-group').show();
            $('#categories_template .record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-categoriesTemplate button').prop('disabled', true);
            $('#filter-categoriesTemplate .results').css('color', '#AAA');
        });

        /**
         * Event: Edit Category Button "Click"
         */
        $('#edit-categoryTemplate').on('click', function () {
            $('#categories_template .add-edit-delete-group').hide();
            $('#categories_template .save-cancel-group').show();
            $('#categories_template .record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-categoriesTemplate button').prop('disabled', true);
            $('#filter-categoriesTemplate .results').css('color', '#AAA');
        });

        /**
         * Event: Delete Category Button "Click"
         */
        $('#delete-categoryTemplate').on('click', function () {
            var category_template_id = $('#categoryTemplate-id').val();

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
                        instance.delete(category_template_id);
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
        $('#save-categoryTemplate').on('click', function () {
            var category_template = {
                name: $('#categoryTemplate-name').val()
            };

            if ($('#categoryTemplate-id').val() !== '') {
                category_template.id = $('#categoryTemplate-id').val();
            }

            if (!instance.validate()) {
                return;
            }

            instance.save(category_template);
        });

        /**
         * Event: Cancel Category Button "Click"
         */
        $('#cancel-categoryTemplate').on('click', function () {
            var id = $('#categoryTemplate-id').val();
            instance.resetForm();
            if (id !== '') {
                instance.select(id, true);
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
    CategoriesTemplateHelper.prototype.filter = function (key, selectId, display) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_service_categories_template';

        $("#categories_template").find(".record-details").hide();

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            key: key,
            limit: this.filterLimit
        };
        $.post(url, data)
            .done(function (response) {
                this.filterResults = response;

                $('#filter-categoriesTemplate .results').empty();

                response.forEach(function (categoryTemplate) {
                    $('#filter-categoriesTemplate .results')
                        .append(this.getFilterHtml(categoryTemplate))
                        .append($('<hr/>'));
                }.bind(this));

                if (response.length === 0) {
                    $('#filter-categoriesTemplate .results').append(
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
                        .appendTo('#filter-categoriesTemplate .results');
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
    CategoriesTemplateHelper.prototype.save = function (category_template) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_service_category_template';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            category_template: JSON.stringify(category_template)
        };

        $.post(url, data)
            .done(function (response) {
                Backend.displayNotification(EALang.service_category_template_saved);
                this.resetForm();
                $('#filter-categoriesTemplate .key').val('');
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
    CategoriesTemplateHelper.prototype.delete = function (id) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_service_category_template';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            category_template_id: id
        };

        $.post(url, data)
            .done(function () {
                Backend.displayNotification(EALang.service_category_template_deleted);

                this.resetForm();
                this.filter($('#filter-categoriesTemplate .key').val());
                BackendServices.updateAvailableCategories();
            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Display a category record on the form.
     *
     * @param {Object} category Contains the category data.
     */
    CategoriesTemplateHelper.prototype.display = function (categoryTemplate) {
        $('#categoryTemplate-id').val(categoryTemplate.id);
        $('#categoryTemplate-name').val(categoryTemplate.name);
        $('#categoryTemplate-image').attr("src",categoryTemplate.image);
    };

    /**
     * Validate category data before save (insert or update).
     *
     * @return {Boolean} Returns the validation result.
     */
    CategoriesTemplateHelper.prototype.validate = function () {
        $('#categoriestemplate .has-error').removeClass('has-error');
        $('#categoriestemplate .form-message')
            .removeClass('alert-danger')
            .hide();

        try {
            var missingRequired = false;

            $('#categoriestemplate .required').each(function (index, requiredField) {
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
            $('#categoriestemplate .form-message')
                .addClass('alert-danger')
                .text(error.message)
                .show();
            return false;
        }
    };

    /**
     * Bring the category form back to its initial state.
     */
    CategoriesTemplateHelper.prototype.resetForm = function () {
        $('#filter-categoriesTemplate .selected').removeClass('selected');
        $('#filter-categoriesTemplate button').prop('disabled', false);
        $('#filter-categoriesTemplate .results').css('color', '');

        $('#categories_template .add-edit-delete-group').show();
        $('#categories_template .save-cancel-group').hide();
        $('#categories_template .record-details')
            .find('input, select, textarea')
            .val('')
            .prop('disabled', true);
        $('#edit-categoryTemplate, #delete-categoryTemplate').prop('disabled', true);

        $('#categories_template .record-details .has-error').removeClass('has-error');
        $('#categories_template .record-details .form-message').hide();

        $('#categoryTemplate-image').attr("src", '');
    };

    /**
     * Get the filter results row HTML code.
     *
     * @param {Object} category Contains the category data.
     *
     * @return {String} Returns the record HTML code.
     */
    CategoriesTemplateHelper.prototype.getFilterHtml = function (categoryTemplate) {
        return $('<div/>', {
            'class': 'categoryTemplate-row entry',
            'data-id': categoryTemplate.id,
            'html': [
                $('<strong/>', {
                    'text': categoryTemplate.name
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
    CategoriesTemplateHelper.prototype.select = function (id, display) {
        display = display || false;

        $('#filter-categoriesTemplate .selected').removeClass('selected');

        $('#filter-categoriesTemplate .categoryTemplate-row[data-id="' + id + '"]').addClass('selected');

        if (display) {
            var category = this.filterResults.find(function (category) {
                return Number(category.id) === Number(id);
            }.bind(this));

            this.display(category);

            $('#edit-categoriestemplate, #delete-categoriestemplate').prop('disabled', false);
        }
    };

    window.CategoriesTemplateHelper = CategoriesTemplateHelper;
})();
