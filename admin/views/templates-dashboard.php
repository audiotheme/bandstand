<?php
/**
 * Dashboard Underscore.js templates.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<script type="text/html" id="tmpl-bandstand-module-toggle-switch">
	<input type="checkbox" id="bandstand-toggle-{{ data.id }}" name="activate">
	<label for="bandstand-toggle-{{ data.id }}"><span class="screen-reader-text">{{ data.label }}</span></label>
</script>
