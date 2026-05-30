jQuery(document).ready(function($) {
    // Test Requestor Button
    $('#internet-melli-test-btn').on('click', function() {
        var $btn = $(this);
        var $result = $('#internet-melli-test-result');
        
        $btn.prop('disabled', true).html('<span class="im-spinner"></span> ' + internetMelli.strings.testing);
        $result.removeClass('success error warning').hide();
        
        // بررسی ریکوئستر در مرورگر کاربر
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                var isRegistered = false;
                var swScope = null;
                
                for (var registration of registrations) {
                    // بررسی registration ما
                    if (registration.active && registration.active.scriptURL) {
                        var scriptUrl = registration.active.scriptURL;
                        // بررسی URL ریکوئستر
                        if (scriptUrl.includes('sw.js') || scriptUrl.includes('internet-melli') || scriptUrl.includes('?sw=')) {
                            isRegistered = true;
                            swScope = registration.scope;
                            break;
                        }
                    }
                }
                
                if (isRegistered) {
                    // ریکوئستر فعال است - سبز
                    $result.addClass('success').html(
                        '<span class="dashicons dashicons-yes-alt"></span> ' + internetMelli.strings.test_success
                    ).show();
                } else {
                    // ریکوئستر رجیستر نشده - نارنجی
                    $result.addClass('warning').html(
                        '<span class="dashicons dashicons-warning"></span> ' + internetMelli.strings.test_inactive
                    ).show();
                }
                
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-yes-alt"></span> ' + internetMelli.strings.testsw);
            }).catch(function(error) {
                console.error('SW Check Error:', error);
                $result.addClass('error').html(
                    '<span class="dashicons dashicons-dismiss"></span> خطا در بررسی ریکوئستر'
                ).show();
                $btn.prop('disabled', false);
            });
        } else {
            // مرورگر پشتیبانی نمی‌کند
            $result.addClass('error').html(
                '<span class="dashicons dashicons-dismiss"></span> مرورگر شما از Requester پشتیبانی نمی‌کند'
            ).show();
            $btn.prop('disabled', false);
        }
    });

    // Save Settings Form
    $('#internet-melli-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $form.find('input[type="submit"]');
        var $message = $('#internet-melli-message');
        var $indicator = $('.im-saving-indicator');
        
        $btn.prop('disabled', true);
        $indicator.show();
        $message.removeClass('show success error warning');
        
        $.ajax({
            url: internetMelli.ajax_url,
            type: 'POST',
            data: {
                action: 'internet_melli_toggle',
                nonce: internetMelli.nonce,
                enabled: $('#internet_melli_enabled').is(':checked') ? 1 : 0,
                blocked_domains_frontend: $('#internet_melli_blocked_domains_frontend').val(),
                blocked_domains_backend: $('#internet_melli_blocked_domains_backend').val(),
                sw_guarantee: $('#internet_melli_sw_guarantee').is(':checked') ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    $message.addClass('show success').html(
                        '<span class="dashicons dashicons-yes-alt"></span> ' + response.data.message
                    );
                    
                    // Reset unsaved changes flag
                    if (window.backendDomainManagerInstance) {
                        window.backendDomainManagerInstance.hasUnsavedChanges = false;
                        $('.im-unsaved-warning').slideUp(function() {
                            $(this).remove();
                        });
                    }
                    
                    // Update status badge
                    if (response.data.enabled == 1) {
                        $('.im-badge').removeClass('im-badge-inactive').addClass('im-badge-active').text('فعال');
                        $('.im-info-activity').text('فعال');
                        $('.im-status-dot').removeClass('im-status-inactive').addClass('im-status-active');
                    } else {
                        $('.im-badge').removeClass('im-badge-active').addClass('im-badge-inactive').text('غیرفعال');
                        $('.im-info-activity').text('غیرفعال');
                        $('.im-status-dot').removeClass('im-status-active').addClass('im-status-inactive');
                    }
                } else {
                    $message.addClass('show error').html(
                        '<span class="dashicons dashicons-dismiss"></span> ' + response.data.message
                    );
                }
            },
            error: function() {
                $message.addClass('show error').html(
                    '<span class="dashicons dashicons-dismiss"></span> ' + internetMelli.strings.error
                );
            },
            complete: function() {
                $btn.prop('disabled', false);
                $indicator.hide();
            }
        });
    });
    
});


/* ===== Domain Manager ===== */

/* === Global Alert System === */
(function($) {
    'use strict';
    
    var GlobalAlertManager = {
        $container: null,
        isVisible: false,
        
        init: function() {
            if (!$('#im-global-alert-container').length) {
                var containerHtml = '<div id="im-global-alert-container" style="position:fixed;top:32px;right:160px;left:160px;z-index:99999;display:none;">' +
                    '<div class="notice notice-warning im-global-alert" style="margin:0;box-shadow:0 2px 8px rgba(0,0,0,0.2);">' +
                    '<p style="margin:8px 0;"><span class="dashicons dashicons-warning"></span> ' +
                    '<strong id="im-global-alert-message"></strong></p>' +
                    '</div></div>';
                $('body').append(containerHtml);
            }
            this.$container = $('#im-global-alert-container');
        },
        
        show: function(message) {
            if (!this.$container) this.init();
            $('#im-global-alert-message').text(message);
            this.$container.slideDown();
            this.isVisible = true;
        },
        
        hide: function() {
            if (!this.$container) return;
            this.$container.slideUp(function() {
                $(this).hide();
            });
            this.isVisible = false;
        },
        
        update: function(hasChanges, message) {
            if (hasChanges && !this.isVisible) {
                this.show(message);
            } else if (!hasChanges && this.isVisible) {
                this.hide();
            }
        }
    };
    
    window.IMGlobalAlert = GlobalAlertManager;
    
})(jQuery);

/* ===== Domain Manager (Frontend) ===== */
(function($) {
    'use strict';
    
    function DomainManager(options) {
        this.$hiddenInput = $(options.hiddenFieldSelector);
        this.$input = $(options.inputSelector);
        this.$addBtn = $(options.addButtonSelector);
        this.$container = $(options.listContainerSelector);
        this.$saveBtn = $(options.saveButtonSelector);
        
        if (!this.$hiddenInput.length || !this.$container.length) {
            console.log('Internet Melli: Domain elements not found for selector ', options.hiddenFieldSelector);
            return;
        }
        
        this.originalDomains = [];
        this.hasUnsavedChanges = false;
        
        this.init();
    }
    
    DomainManager.prototype.getDomains = function() {
        var val = this.$hiddenInput.val() || '';
        if (!val) return [];
        return val.split(',')
            .map(function(domain) { return domain.trim(); })
            .filter(function(domain) { return domain.length > 0; });
    };
    
    DomainManager.prototype.saveDomains = function(domains) {
        this.$hiddenInput.val(domains.join(','));
    };
    
    DomainManager.prototype.checkForChanges = function() {
        var currentDomains = this.getDomains();
        var hasChanges = JSON.stringify(currentDomains) !== JSON.stringify(this.originalDomains);
        
        this.hasUnsavedChanges = hasChanges;
        
        window.IMGlobalAlert.update(
            hasChanges,
            '⚠️ تغییرات شما هنوز ذخیره نشده است. لطفاً روی دکمه "ذخیره" کلیک کنید.'
        );
    };
    
    DomainManager.prototype.onSave = function() {
        this.originalDomains = this.getDomains();
        this.hasUnsavedChanges = false;
        window.IMGlobalAlert.hide();
    };
    
    DomainManager.prototype.renderDomains = function() {
        var domains = this.getDomains();
        var $container = this.$container;
        
        if (domains.length === 0) {
            $container.html('<p class="im-empty-message">هنوز دامنه‌ای اضافه نشده است</p>');
            return;
        }
        
        var html = '';
        html += '<div class="im-domains-header">';
        html += '<span>دامنه‌های مسدود شده</span>';
        html += '<span class="im-domains-count">' + domains.length + ' عدد</span>';
        html += '</div>';
        html += '<ul class="im-domains-list">';
        
        domains.forEach(function(domain, index) {
            html += '<li class="im-domain-item" data-index="' + index + '">';
            html += '<span class="im-domain-text">' + domain + '</span>';
            html += '<button type="button" class="im-domain-remove" data-index="' + index + '">&times;</button>';
            html += '</li>';
        });
        
        html += '</ul>';
        $container.html(html);
    };
    
    DomainManager.prototype.init = function() {
        var self = this;
        
        window.IMGlobalAlert.init();
        this.originalDomains = this.getDomains();
        this.renderDomains();
        
        this.$addBtn.on('click', function() {
            var domain = (self.$input.val() || '').trim();
            if (!domain) return;
            
            var domains = self.getDomains();
            if (domains.indexOf(domain) === -1) {
                domains.push(domain);
                self.saveDomains(domains);
                self.renderDomains();
                self.checkForChanges();
            }
            self.$input.val('');
        });
        
        this.$container.on('click', '.im-domain-remove', function() {
            var index = parseInt($(this).data('index'), 10);
            var domains = self.getDomains();
            if (index >= 0 && index < domains.length) {
                domains.splice(index, 1);
                self.saveDomains(domains);
                self.renderDomains();
                self.checkForChanges();
            }
        });
        
        // اتصال به دکمه ذخیره
        if (this.$saveBtn && this.$saveBtn.length) {
            this.$saveBtn.on('click', function() {
                self.onSave();
            });
        }
        
        $(window).on('beforeunload', function(e) {
            if (self.hasUnsavedChanges) {
                var message = 'تغییرات شما ذخیره نشده است. آیا مطمئن هستید؟';
                e.returnValue = message;
                return message;
            }
        });
    };
    
    $(function() {
        window.frontendDomainManagerInstance = new DomainManager({
            hiddenFieldSelector: '#internet_melli_blocked_domains_frontend',
            inputSelector: '#domain_input_frontend',
            addButtonSelector: '#add_domain_btn_frontend',
            listContainerSelector: '#domains_list_frontend',
            saveButtonSelector: '#internet_melli_submit' // ← دکمه ذخیره
        });
    });
    
})(jQuery);

/* ===== Backend Domain Manager (JSON version) ===== */
(function($) {
    'use strict';
    
    function BackendDomainManager(options) {
        this.$hiddenInput = $(options.hiddenFieldSelector);
        this.$input = $(options.inputSelector);
        this.$addBtn = $(options.addButtonSelector);
        this.$list = $(options.listContainerSelector);
        this.$saveBtn = $(options.saveButtonSelector);
        
        this.domains = [];
        this.originalDomains = [];
        this.hasUnsavedChanges = false;
        
        this.init();
    }
    
    BackendDomainManager.prototype.init = function() {
        var self = this;
        
        this.load();
        this.originalDomains = JSON.parse(JSON.stringify(this.domains));
        
        this.render();
        this.attachEvents();
        
        // اتصال به دکمه ذخیره
        if (this.$saveBtn && this.$saveBtn.length) {
            this.$saveBtn.on('click', function() {
                self.onSave();
            });
        }
        
        $(window).on('beforeunload', function(e) {
            if (self.hasUnsavedChanges) {
                var message = 'تغییرات شما ذخیره نشده است. آیا مطمئن هستید؟';
                e.returnValue = message;
                return message;
            }
        });
    };
    
    BackendDomainManager.prototype.load = function() {
        var raw = this.$hiddenInput.val().trim();
        if (!raw) {
            this.domains = [];
            return;
        }
        try {
            this.domains = JSON.parse(raw);
            if (!Array.isArray(this.domains)) this.domains = [];
        } catch (e) {
            console.error("BackendDomainManager JSON parse error:", e, raw);
            this.domains = [];
        }
    };
    
    BackendDomainManager.prototype.save = function() {
        this.$hiddenInput.val(JSON.stringify(this.domains));
    };
    
    BackendDomainManager.prototype.checkForChanges = function() {
        var hasChanges = JSON.stringify(this.domains) !== JSON.stringify(this.originalDomains);
        
        this.hasUnsavedChanges = hasChanges;
        
        window.IMGlobalAlert.update(
            hasChanges,
            '⚠️ تغییرات شما هنوز ذخیره نشده است. لطفاً روی دکمه "ذخیره تنظیمات" کلیک کنید.'
        );
    };
    
    BackendDomainManager.prototype.onSave = function() {
        this.originalDomains = JSON.parse(JSON.stringify(this.domains));
        this.hasUnsavedChanges = false;
        window.IMGlobalAlert.hide();
    };
    
BackendDomainManager.prototype.render = function() {
    if (!this.domains.length) {
        this.$list.html('<p class="im-empty-message">هنوز دامنه‌ای اضافه نشده است</p>');
        return;
    }
    var html = '<div class="im-domains-header">' +
        '<span>دامنه‌های مسدود شده</span>' +
        '<span class="im-domains-count">' + this.domains.length + ' عدد</span>' +
        '</div>';
    html += '<ul class="im-domains-list">';
    var self = this;
    
    this.domains.forEach(function(item, index) {
        html += '<li class="im-domain-item" data-index="' + index + '">';
        
        html += '<span class="im-domain-text">' + item.domain + '</span>';
        
        html += '<div class="im-domain-actions">';

        
        // Toggle
        html += '<label class="im-toggle">';
        html += '<input type="checkbox" class="im-domain-enabled" data-index="' + index + '"';
        if (item.enabled) html += ' checked';
        html += '>';
        html += '<span class="im-toggle-slider">';
        html += '<span class="im-toggle-text">' + (item.enabled ? 'Block' : 'Open') + '</span>';
        html += '</span>';
        html += '</label>';
        
                
        // دکمه حذف
        html += '<button type="button" class="im-domain-remove" data-index="' + index + '">';
        html += '<span class="dashicons dashicons-no-alt"></span>';
        html += '</button>';
        
        html += '</div>'; // end .im-domain-actions
        
        html += '</li>';
    });
    
    html += '</ul>';
    this.$list.html(html);
    this.attachEvents();
};
// اضافه کردن event listener بعد از ساخت HTML
$(document).on('change', '.im-domain-enabled', function() {
    var $checkbox = $(this);
    var $text = $checkbox.siblings('.im-toggle-slider').find('.im-toggle-text');
    
    if ($checkbox.is(':checked')) {
        $text.text('Block');
    } else {
        $text.text('Open');
    }
});
        
    
    BackendDomainManager.prototype.attachEvents = function() {
        var self = this;
        
        this.$addBtn.off('click');
        this.$list.off('change', '.im-domain-enabled');
        this.$list.off('click', '.im-domain-remove');
        
        this.$addBtn.on('click', function() {
            var domain = self.$input.val().trim();
            if (!domain) return;
            
            self.domains.push({ domain: domain, enabled: true });
            self.save();
            self.render();
            self.checkForChanges();
            self.$input.val('');
        });
        
        this.$list.on('change', '.im-domain-enabled', function() {
            var index = $(this).data('index');
            self.domains[index].enabled = this.checked;
            self.save();
            self.checkForChanges();
        });
        
        this.$list.on('click', '.im-domain-remove', function() {
            var index = $(this).data('index');
            self.domains.splice(index, 1);
            self.save();
            self.render();
            self.checkForChanges();
        });
    };
    
    $(function() {
        window.IMGlobalAlert.init();
        
        window.backendDomainManagerInstance = new BackendDomainManager({
            hiddenFieldSelector: '#internet_melli_blocked_domains_backend',
            inputSelector: '#domain_input_backend',
            addButtonSelector: '#add_domain_btn_backend',
            listContainerSelector: '#domains_list_backend',
            saveButtonSelector: '#internet_melli_submit' // ← دکمه ذخیره
        });
    });
    
})(jQuery);


// ===== Update Checker =====
(function($) {
    'use strict';
    
    var $imCheckBtn = $('#im-check-update-btn');
    var $imInstallBtn = $('#im-install-update-btn');
    var $imCheckResult = $('#im-check-result');
    var $imUpdateSection = $('#im-update-section');
    var $imUpdateLoading = $('#im-update-loading');
    var $imUpdateLoadingText = $('#im-update-loading-text');
    var $imUpdateMessage = $('#im-update-message');
    var $imNewVersionDisplay = $('#im-new-version-display');
    var $imReleaseNotes = $('#im-release-notes');

    var cachedUpdateData = null;

    // چک کردن آپدیت
    $imCheckBtn.on('click', function() {
        checkForUpdate();
    });

    // نصب آپدیت
    $imInstallBtn.on('click', function() {
        if (confirm(internetMelli.strings.update_confirm)) {
            installUpdate();
        }
    });

    function checkForUpdate() {
        // افزودن حالت لودینگ به دکمه
        $imCheckBtn.prop('disabled', true).addClass('updating');
        $imCheckBtn.html('<span class="im-btn-spinner"></span> ' + internetMelli.strings.checking);
        
        $imCheckResult.removeClass('show error success');
        $imUpdateSection.hide();
        $imUpdateMessage.removeClass('show');

        $.ajax({
            url: internetMelli.ajax_url,
            type: 'POST',
            data: {
                action: 'check_plugin_update',
                nonce: internetMelli.nonce
            },
            success: function(response) {
                cachedUpdateData = response;
                
                if (response.status === 'success') {
                    if (response.has_update) {
                        $imCheckResult.html(
                            '<span class="dashicons dashicons-yes-alt"></span> ' + 
                            internetMelli.strings.update_available
                        ).addClass('show success');

                        $imNewVersionDisplay.text('v' + response.new_version);
                        
                        if (response.release_notes) {
                            $imReleaseNotes.html('<h4>' + internetMelli.strings.release_notes + '</h4>' + response.release_notes);
                        } else {
                            $imReleaseNotes.hide();
                        }
                        
                        $imUpdateSection.slideDown();
                    } else {
                        $imCheckResult.html(
                            '<span class="dashicons dashicons-yes"></span> ' + 
                            internetMelli.strings.no_update
                        ).addClass('show success');
                    }
                } else {
                    $imCheckResult.html(
                        '<span class="dashicons dashicons-warning"></span> ' + 
                        response.message
                    ).addClass('show error');
                }
            },
            error: function() {
                $imCheckResult.html(
                    '<span class="dashicons dashicons-warning"></span> ' + 
                    internetMelli.strings.error
                ).addClass('show error');
            },
            complete: function() {
                // برگرداندن دکمه به حالت اول
                $imCheckBtn.prop('disabled', false).removeClass('updating');
                $imCheckBtn.html('<span class="dashicons dashicons-update"></span> ' + internetMelli.strings.check_update);
            }
        });
    }

    function installUpdate() {
        if (!cachedUpdateData || !cachedUpdateData.download_url) {
            $imUpdateMessage.html(internetMelli.strings.no_download_url).addClass('show error');
            return;
        }

        $imInstallBtn.prop('disabled', true).addClass('updating');
        $imInstallBtn.html('<span class="im-btn-spinner"></span> ' + internetMelli.strings.updating);
        $imUpdateLoading.show();
        $imUpdateLoadingText.text(internetMelli.strings.updating);

        $.ajax({
            url: internetMelli.ajax_url,
            type: 'POST',
            data: {
                action: 'install_plugin_update',
                nonce: internetMelli.nonce,
                download_url: cachedUpdateData.download_url
            },
            success: function(response) {
                if (response.status === 'success') {
                    $imUpdateMessage.html(
                        '<span class="dashicons dashicons-yes-alt"></span> ' + 
                        response.message
                    ).addClass('show success');
                    
                    $imUpdateSection.slideUp();
                    
                    setTimeout(function() {
   						 window.location.reload();
									}, 500);

                } else {
                    $imUpdateMessage.html(
                        '<span class="dashicons dashicons-warning"></span> ' + 
                        response.message
                    ).addClass('show error');
                }
            },
            error: function() {
                $imUpdateMessage.html(
                    '<span class="dashicons dashicons-warning"></span> ' + 
                    internetMelli.strings.error
                ).addClass('show error');
            },
            complete: function() {
                $imInstallBtn.prop('disabled', false).removeClass('updating');
                $imInstallBtn.html('<span class="dashicons dashicons-download"></span> ' + internetMelli.strings.install_update);
                $imUpdateLoading.hide();
            }
        });
    }

})(jQuery);



jQuery(function($) {

    $('#internet_melli_delete_all_btn').on('click', function() {

        if (!confirm('آیا مطمئن هستید؟ تمام اطلاعات پلاگین برای همیشه حذف می‌شود.')) {
            return;
        }

        $('#internet_melli_delete_all_btn').prop('disabled', true);

        $.ajax({
            url: internetMelli.ajax_url,
            type: 'POST',
            data: {
                action: 'internet_melli_delete_all',
                nonce: internetMelli.nonce
            },
            success: function(response) {
                $('#internet_melli_delete_all_result')
                    .text(response.data.message || 'انجام شد.')
                    .css('color', 'green')
                    .show();

                $('#internet_melli_delete_all_btn').prop('disabled', false);
            },
            error: function() {
                $('#internet_melli_delete_all_result')
                    .text('خطا در حذف اطلاعات.')
                    .css('color', 'red')
                    .show();

                $('#internet_melli_delete_all_btn').prop('disabled', false);
            }
        });

    });

});

//feedback
jQuery(document).ready(function($) {

    // فرم فیدبک
    $('#im-feedback-form').on('submit', function(e) {
        e.preventDefault();

        var $form   = $(this);
        var $btn    = $('#im-send-feedback-btn');
        var $result = $('#im-feedback-result');
        var $loading = $('#im-feedback-loading');

        $result.hide().text('');
        $loading.show();
        $btn.prop('disabled', true);

        $.ajax({
            url: internetMelli.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'im_send_feedback',
                im_feedback_nonce: $('#im_feedback_nonce').val(),
                text: $('#im-feedback-text').val(),
                user: $('input[name="user"]').val()
            },

            success: function(res) {
                $loading.hide();
                $btn.prop('disabled', false);

                if (res.success) {
                    $result
                        .css('color', 'green')
                        .text(res.data.message)
                        .show();
                    $form[0].reset();
                } else {
                    $result
                        .css('color', 'red')
                        .text(res.data.message)
                        .show();
                }
            },

            error: function() {
                $loading.hide();
                $btn.prop('disabled', false);
                $result
                    .css('color', 'red')
                    .text('خطا در ارتباط با سرور.')
                    .show();
            }
        });

    });

});
