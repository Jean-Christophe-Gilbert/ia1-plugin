<!-- Widget de chat IA1 -->
<div class="ia1-chat-widget" data-height="<?php echo esc_attr( $atts['height'] ); ?>">
    
    <!-- En-tête du chat -->
    <div class="ia1-chat-header">
        <div class="ia1-chat-avatar">
            <?php echo esc_html( $customization['avatar_initials'] ); ?>
        </div>
        <div class="ia1-chat-info">
            <p class="ia1-chat-subtitle"><?php echo esc_html( $customization['assistant_subtitle'] ); ?></p>
        </div>
    </div>
    
    <!-- Corps du chat (messages) -->
    <div class="ia1-chat-body" style="height: <?php echo esc_attr( $atts['height'] ); ?>;">
        <!-- Message de bienvenue -->
        <div class="ia1-message ia1-message-assistant">
            <div class="ia1-message-avatar">
                <?php echo esc_html( substr( $customization['avatar_initials'], 0, 1 ) ); ?>
            </div>
            <div class="ia1-message-content">
                <div class="ia1-message-text">
                    <?php echo esc_html( $customization['welcome_message'] ); ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Zone de saisie -->
    <div class="ia1-chat-input-container">
        <div class="ia1-chat-input-wrapper">
            <textarea 
                class="ia1-chat-input" 
                placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>"
                rows="1"
                maxlength="1000"></textarea>
            <button class="ia1-chat-send-btn" type="button" title="Envoyer">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                </svg>
            </button>
        </div>
        <div class="ia1-chat-info-text">
            Propulsé par <a href="https://ia1.fr" target="_blank">IA1</a> • 
            <a href="https://mistral.ai" target="_blank">Mistral AI</a>
        </div>
    </div>
    
    <!-- Loader -->
    <div class="ia1-chat-loader" style="display: none;">
        <div class="ia1-chat-loader-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    
</div>
