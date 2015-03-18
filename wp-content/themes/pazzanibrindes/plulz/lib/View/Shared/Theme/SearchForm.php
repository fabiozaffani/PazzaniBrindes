<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" >
    <input type="text" class="field" name="s" id="s" placeholder="<?php esc_attr_e( 'Procurar...', $this->_name ); ?>" value="<?php echo isset($this->SearchQuery) ? $this->SearchQuery : ''; ?>" />
    <input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'OK', $this->_name ); ?>" />
</form>