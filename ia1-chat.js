/**
 * JavaScript du widget de chat IA1
 *
 * @package IA1
 * @version 3.1.10
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Pour chaque widget de chat sur la page
        $('.ia1-chat-widget').each(function() {
            initChatWidget($(this));
        });
        
        /**
         * Initialise un widget de chat
         */
        function initChatWidget($widget) {
            const $body = $widget.find('.ia1-chat-body');
            const $input = $widget.find('.ia1-chat-input');
            const $sendBtn = $widget.find('.ia1-chat-send-btn');
            const $loader = $widget.find('.ia1-chat-loader');
            
            // Auto-resize du textarea
            $input.on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Envoi avec Enter (Shift+Enter pour nouvelle ligne)
            $input.on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            
            // Envoi avec le bouton
            $sendBtn.on('click', sendMessage);
            
            /**
             * Envoie un message
             */
            function sendMessage() {
                const question = $input.val().trim();
                
                if (!question) {
                    return;
                }
                
                // Désactiver l'input et le bouton
                $input.prop('disabled', true);
                $sendBtn.prop('disabled', true);
                
                // Afficher le message de l'utilisateur
                appendMessage('user', question);
                
                // Vider l'input
                $input.val('').css('height', 'auto');
                
                // Afficher le loader
                $loader.show();
                scrollToBottom();
                
                // Envoyer la requête AJAX
                $.ajax({
                    url: ia1Chat.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'ia1_chat_query',
                        nonce: ia1Chat.nonce,
                        question: question
                    },
                    success: function(response) {
                        if (response.success) {
                            appendMessage('assistant', response.data.response, response.data.sources);
                        } else {
                            appendMessage('assistant', '❌ ' + (response.data.message || 'Une erreur est survenue'));
                        }
                    },
                    error: function() {
                        appendMessage('assistant', '❌ Oups, je n\'arrive pas à répondre pour le moment. Veuillez patienter quelques secondes et réessayer.');
                    },
                    complete: function() {
                        $loader.hide();
                        $input.prop('disabled', false);
                        $sendBtn.prop('disabled', false);
                        $input.focus();
                    }
                });
            }
            
            /**
             * Ajoute un message au chat
             */
            function appendMessage(type, text, sources) {
                const avatarInitial = type === 'user' 
                    ? (ia1Chat.settings.assistant_name ? ia1Chat.settings.assistant_name.charAt(0) : 'U')
                    : (ia1Chat.settings.avatar_initials ? ia1Chat.settings.avatar_initials.charAt(0) : 'IA');
                
                const $message = $('<div>')
                    .addClass('ia1-message ia1-message-' + type);
                
                const $avatar = $('<div>')
                    .addClass('ia1-message-avatar')
                    .text(avatarInitial);
                
                const $content = $('<div>')
                    .addClass('ia1-message-content');
                
                const $text = $('<div>')
                    .addClass('ia1-message-text')
                    .html(formatText(text));
                
                $content.append($text);
                
                // Ajouter les sources si disponibles (VERSION AMÉLIORÉE)
                if (sources && sources.length > 0) {
                    const $sources = $('<div>')
                        .addClass('ia1-message-sources');
                    
                    const $sourcesTitle = $('<div>')
                        .addClass('ia1-message-sources-title')
                        .html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg> Sources :');
                    
                    // Créer une liste à puces UL
                    const $sourcesList = $('<ul>')
                        .addClass('ia1-message-sources-list');
                    
                    sources.forEach(function(source) {
                        const $li = $('<li>');
                        
                        const $link = $('<a>')
                            .addClass('ia1-message-source-link')
                            .attr('href', source.url)
                            .attr('target', '_blank')
                            .attr('rel', 'noopener noreferrer')
                            .html(source.title + ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>');
                        
                        $li.append($link);
                        $sourcesList.append($li);
                    });
                    
                    $sources.append($sourcesTitle).append($sourcesList);
                    $content.append($sources);
                }
                
                $message.append($avatar).append($content);
                
                $body.append($message);
                scrollToBottom();
            }
            
            /**
             * Formate le texte (détecte les URLs et les transforme en liens)
             */
            function formatText(text) {
                // Échapper les caractères HTML
                const div = document.createElement('div');
                div.textContent = text;
                let escaped = div.innerHTML;
                
                // Détecter et transformer les URLs en liens
                const urlRegex = /(https?:\/\/[^\s]+)/g;
                escaped = escaped.replace(urlRegex, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
                
                // Retour à la ligne
                escaped = escaped.replace(/\n/g, '<br>');
                
                return escaped;
            }
            
            /**
             * Scroll vers le bas du chat
             */
            function scrollToBottom() {
                $body.animate({
                    scrollTop: $body[0].scrollHeight
                }, 300);
            }
        }
        
    });
    
})(jQuery);
