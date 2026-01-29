/**
 * JavaScript de l'interface d'administration IA1
 *
 * @package IA1
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // ========== PRÉVISUALISATION EN TEMPS RÉEL ==========
        
        // Nom de l'assistant
        $('#assistant_name').on('input', function() {
            const name = $(this).val() || 'IA1';
            $('#preview-name').text(name);
            
            // Mise à jour auto des initiales si vide
            if (!$('#avatar_initials').val()) {
                const initials = name.charAt(0).toUpperCase();
                $('#preview-avatar').text(initials);
            }
        });
        
        // Sous-titre
        $('#assistant_subtitle').on('input', function() {
            const subtitle = $(this).val() || 'Votre assistante IA locale';
            $('#preview-subtitle').text(subtitle);
        });
        
        // Message de bienvenue
        $('#welcome_message').on('input', function() {
            const message = $(this).val() || 'Bonjour ! Comment puis-je vous aider ?';
            $('#preview-message').text(message);
        });
        
        // Couleur principale
        $('#primary_color_picker, #primary_color').on('input', function() {
            const color = $(this).val();
            
            // Valider le format hex
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                $('#primary_color_picker').val(color);
                $('#primary_color').val(color);
                $('.ia1-chat-header, .ia1-chat-avatar').css('background-color', color);
            }
        });
        
        // Initiales avatar
        $('#avatar_initials').on('input', function() {
            const initials = $(this).val().toUpperCase() || $('#assistant_name').val().charAt(0).toUpperCase() || 'IA';
            $('#preview-avatar').text(initials);
        });
        
        // ========== SAUVEGARDE DES PARAMÈTRES (DÉSACTIVÉ - Utilise submit standard) ==========
        
        // La sauvegarde se fait maintenant via le submit standard du formulaire
        // pour éviter les problèmes de compatibilité
        
        /*
        $('#ia1-settings-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            
            // Récupérer toutes les données du formulaire
            const formData = {
                api_key: $('#api_key').val(),
                model: $('#model').val(),
                assistant_name: $('#assistant_name').val(),
                assistant_subtitle: $('#assistant_subtitle').val(),
                welcome_message: $('#welcome_message').val(),
                primary_color: $('#primary_color').val(),
                avatar_initials: $('#avatar_initials').val(),
                system_prompt: $('#system_prompt').val(),
                use_wikipedia: $('#use_wikipedia').is(':checked')
            };
            
            // Désactiver le bouton
            $submitBtn.prop('disabled', true).text('Enregistrement...');
            
            // Envoi AJAX
            $.ajax({
                url: ia1Admin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ia1_save_settings',
                    nonce: ia1Admin.nonce,
                    data: formData
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.data.message);
                    } else {
                        showNotification('error', response.data.message || 'Erreur lors de la sauvegarde');
                    }
                },
                error: function() {
                    showNotification('error', 'Erreur de connexion au serveur');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
        */
        
        // ========== RÉINITIALISATION ==========
        
        $('#ia1-reset-customization').on('click', function() {
            if (!confirm('Êtes-vous sûr de vouloir réinitialiser la personnalisation aux valeurs par défaut ?')) {
                return;
            }
            
            const $btn = $(this);
            const originalText = $btn.text();
            
            $btn.prop('disabled', true).text('Réinitialisation...');
            
            $.ajax({
                url: ia1Admin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ia1_reset_customization',
                    nonce: ia1Admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Mettre à jour les champs
                        const settings = response.data.settings;
                        $('#assistant_name').val(settings.assistant_name).trigger('input');
                        $('#assistant_subtitle').val(settings.assistant_subtitle).trigger('input');
                        $('#welcome_message').val(settings.welcome_message).trigger('input');
                        $('#primary_color').val(settings.primary_color).trigger('input');
                        $('#avatar_initials').val(settings.avatar_initials).trigger('input');
                        
                        showNotification('success', response.data.message);
                    } else {
                        showNotification('error', response.data.message || 'Erreur lors de la réinitialisation');
                    }
                },
                error: function() {
                    showNotification('error', 'Erreur de connexion au serveur');
                },
                complete: function() {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // ========== FONCTIONS UTILITAIRES ==========
        
        /**
         * Affiche une notification
         */
        function showNotification(type, message) {
            const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
            
            const $notice = $('<div>')
                .addClass('notice ' + noticeClass + ' is-dismissible')
                .html('<p>' + message + '</p>');
            
            $('#ia1-notifications').html($notice);
            
            // Ajouter le bouton de fermeture
            $notice.append('<button type="button" class="notice-dismiss"></button>');
            
            // Gérer la fermeture
            $notice.find('.notice-dismiss').on('click', function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            });
            
            // Auto-fermeture après 5 secondes
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Scroll vers le haut
            $('html, body').animate({
                scrollTop: $('#ia1-notifications').offset().top - 50
            }, 300);
        }
        
        // ========== VALIDATION EN TEMPS RÉEL ==========
        
        // Limiter la longueur du nom (30 caractères)
        $('#assistant_name').on('input', function() {
            if ($(this).val().length > 30) {
                $(this).val($(this).val().substring(0, 30));
            }
        });
        
        // Limiter la longueur du sous-titre (60 caractères)
        $('#assistant_subtitle').on('input', function() {
            if ($(this).val().length > 60) {
                $(this).val($(this).val().substring(0, 60));
            }
        });
        
        // Limiter les initiales (3 caractères max)
        $('#avatar_initials').on('input', function() {
            let val = $(this).val().toUpperCase();
            if (val.length > 3) {
                val = val.substring(0, 3);
            }
            $(this).val(val);
        });
        
        // Validation de la couleur hex
        $('#primary_color').on('blur', function() {
            const val = $(this).val();
            if (!/^#[0-9A-F]{6}$/i.test(val)) {
                showNotification('error', 'Format de couleur invalide. Utilisez le format hexadécimal (ex: #2271b1)');
                $(this).val('#2271b1').trigger('input');
            }
        });
        
    });
    
})(jQuery);
