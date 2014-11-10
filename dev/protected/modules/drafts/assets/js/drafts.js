(function ($) {
    $.fn.saveDraft = function (options) {
        var settings = $.extend({
            createUrl: '',
            getUrl: '',
            deleteUrl: '',
            timeDiff: 60,
            draftMessage: '',
            notifyTime: 0
        }, options);

        var form = $(this), formId = form.attr('id'), formData = {}, formFields = [], needDraft = true, localData, savedData, savedLocalData, loaded = false, xhr = {};

        xhr[formId] = null;

        /**
         * save draft before window unload
         * @param e
         */
        window.onbeforeunload = function (e) {
            $.fn.saveDraft.save();
        }

        /**
         * on submit save draft and modify form action to not load draft after submit
         */
        form.submit(function (e) {
            e.preventDefault();

            $.fn.saveDraft.save();
            var action = $(this).attr('action');
            $(this).attr('action', action + '#draftLoaded=1');
            this.submit();
        });

        if (document.location.href.indexOf('draftLoaded', 0) > 0) {
            loaded = true;
            needDraft = false;
        }

        /**
         * if need draft load then get it
         */
        if (needDraft) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: settings.getUrl,
                data: {formId: formId },
                /**
                 * do not show any errors if draft not loaded
                 */
                suppressErrors: true,
                success: function (data) {
                    savedLocalData = $.fn.saveDraft.getLocalData();

                    if (Object.keys(data.data).length) {
                        /**
                         * we need to go deeper
                         */
                        savedData = data.data.data;

                        if (data.data.deleted) {
                            $.fn.saveDraft.deleteDraft();
                        }
                        else {
                            /**
                             * if server data more than settings.timeDiff seconds older we use local data
                             */
                            if (( savedLocalData.mtime - data.data.mtime ) > settings.timeDiff) {
                                $.fn.saveDraft.putFormData(savedLocalData);
                            }
                            else {
                                $.fn.saveDraft.putFormData(savedData);
                            }
                        }
                    }
                    else {
                        $.fn.saveDraft.putFormData(savedLocalData);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    loaded = true;
                },
                complete: function () {
                    loaded = true;
                }
            });
        }


        /**
         * collect inputs and set change event to save draft
         */
        form.find(':input').each(function () {
            if ($(this).attr('name') != 'csrf') {
                formFields.push($(this));
            }
            $(this).change(function () {
                $.fn.saveDraft.save();
            });
        });

        /**
         * delete draft from server and local storage
         */
        $.fn.saveDraft.deleteDraft = function () {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: settings.deleteUrl,
                data: {formId: formId },
                success: function (data) {
                },
                error: function () {

                }
            });
            localStorage && localStorage.removeItem('draft' + formId);
        }

        /**
         * save draft
         * @returns {boolean}
         */
        $.fn.saveDraft.save = function () {
            if (!loaded) {
                return true;
            }
            formData = {};
            $.each(formFields, function () {
                /**
                 * if this is CSRF tag or submit button or hidden tag but not select2 hidden
                 * we skip it
                 */
                if (this.attr('name') == 'csrf' || this.attr('type') == 'submit' || ( this.attr('type') == 'hidden' && !this.hasClass('select2-offscreen') )) {
                    return true;
                }
                if (this.attr('type') == 'checkbox' && this.attr('checked') != 'checked') {
                    return true;
                }
                if (this.val() == '') {
                    return true;
                }
                formData[this.attr('name')] = this.val();
            });

            if (Object.keys(formData).length) {

                if (xhr[formId] != null) {
                    xhr[formId].abort();
                }

                xhr[formId] = $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: settings.createUrl,
                    data: {formId: formId, data: JSON.stringify(formData) },
                    success: function (data) {
                    }
                });

                formData['mtime'] = parseInt(new Date().getTime() / 1000);
                localStorage && localStorage.setItem('draft' + formId, JSON.stringify(formData));
            }
        }

        /**
         * get data from local storage if present
         * @returns {*}
         */
        $.fn.saveDraft.getLocalData = function () {
            try {
                return localStorage && JSON.parse(localStorage.getItem('draft' + formId));
            }
            catch (e) {
                return localData['mtime'] = 0;
            }
        }

        /**
         * put data into form
         * @param data
         */
        $.fn.saveDraft.putFormData = function (data) {
            if (!data || !Object.keys(data).length) {
                return false;
            }
            $('.top-right').notify({
                message: { html: settings.draftMessage },
                closable: true,
                fadeOut: {
                    enabled: true,
                    delay: settings.notifyTime
                },
                type: 'success'
            }).show();

            $(document).on('click', '[data-action="load-draft"]', function (e) {
                e.preventDefault();

                $('.top-right > .alert').remove();

                form.find(':input').each(function () {
                    var elem = $(this);
                    $.each(data, function (key, val) {
                        if (elem.attr('name') == key) {
                            if (elem.attr('type') == 'checkbox') {
                                elem.attr('checked', 'checked');
                            }
                            else if (elem.get(0).tagName.toLowerCase() == 'textarea') {
                                elem.val(val);
                                /**
                                 * костыль для imperavi
                                 */
                                if (elem.redactor !== undefined) {
                                    elem.redactor('set', val);
                                }
                            }
                            else {
                                elem.val(val);
                            }
                            elem.trigger('change');
                            return false;
                        }
                    });
                });

                loaded = true;
            });
        }

        /**
         * bind events
         */
        $(document).on('click', '[data-action="remove-draft-notify"]', function (e) {
            e.preventDefault();
            $('.top-right > .alert').remove();
        });
    }
}(jQuery));

