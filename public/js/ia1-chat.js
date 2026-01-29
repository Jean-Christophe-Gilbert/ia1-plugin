/**
 * JavaScript du widget de chat IA1
 *
 * @package IA1
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
                    .text(text);
                
                $content.append($text);
                
                // Ajouter les sources si disponibles
                if (sources && sources.length > 0) {
                    const $sources = $('<div>')
                        .addClass('ia1-message-sources');
                    
                    const $sourcesTitle = $('<div>')
                        .addClass('ia1-message-sources-title')
                        .text('Sources :');
                    
                    const $sourcesList = $('<div>')
                        .addClass('ia1-message-sources-list');
                    
                    sources.forEach(function(source) {
                        const $link = $('<a>')
                            .addClass('ia1-message-source-link')
                            .attr('href', source.url)
                            .attr('target', '_blank')
                            .text(source.title);
                        
                        $sourcesList.append($link);
                    });
                    
                    $sources.append($sourcesTitle).append($sourcesList);
                    $content.append($sources);
                }
                
                $message.append($avatar).append($content);
                
                $body.append($message);
                scrollToBottom();
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
