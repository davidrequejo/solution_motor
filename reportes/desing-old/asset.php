<?php
/**
 * Static singleton class for handling Assets
 *
 * @usage
 *     Asset::css('myscript.css');
 *
 *     Asset::css(array(
 *         'my-script.css',
 *         'another.css',
 *     ));
 * 
 *     Asset::js('myjsfile.js');
 * 
 *     Asset::js('another.js');
 * 
 *     // Compile a js file from multiple files
 *     Asset::js_group('app.js', array(
 *         'app.main.js',
 *         'app.additional.js',
 *         'app.helpers.js',
 *     ));
 * 
 *     // Echo <script> and <link>
 *     echo Asset::css_includes();
 *     echo Asset::js_includes();
 */
 
class Asset {
	
	public static $css = array();
	public static $css_groups = array();
	public static $js = array();
	
	public static function css($file)
	{
		// Add single css
		if (is_string($file))
		{
			Asset::$css[] = $file;
			return;
		}
		
		// Merge array
		Asset::$css += $file;
	}
	
	public static function css_group($group, $file)
	{
		// Add single css
		if (is_string($file))
		{
			Asset::$css_groups[$group][] = $file;
			return;
		}
		
		// If not set
		if ( ! isset(Asset::$css_groups[$group]))
		{
			Asset::$css_groups[$group] = array();
		}
		
		// Merge array
		Asset::$css_groups[$group] += $file;
	}
	
	public static function js($file)
	{
		// Add single css
		if (is_string($file))
		{
			Asset::$js[] = $file;
			return;
		}
		
		// Merge array
		Asset::$js += $file;
	}
	
	public static function js_group($group, $file)
	{
		// Add single css
		if (is_string($file))
		{
			Asset::$js_groups[$group][] = $file;
			return;
		}
		
		// If not set
		if ( ! isset(Asset::$js_groups[$group]))
		{
			Asset::$js_groups[$group] = array();
		}
		
		// Merge array
		Asset::$js_groups[$group] += $file;
	}
	
	public static function css_includes()
	{
		// Compile sass if neccessary
		Asset::compile_sass();
		
		// Compile groups
		Assets::compile_groups();
		
		// Output
		$css = array();
		
		//
		foreach (Asset::$css as $_css)
		{
			$css[] = '<link rel="stylesheet" href="'.$_css.'">';
		}
		
		// Return css includes
		return implode("\n", $css);
	}
	
	public static function js_includes()
	{
		// Output
		$js = array();
		
		// Compile groups
		Assets::compile_groups();
		
		//
		foreach (Asset::$js as $_js)
		{
			$js[] = '<script src="'.$_js.'"></script>';
		}
		
		// Return css includes
		return implode("\n", $js);
	}
	
	public static function compile_groups()
	{
		// compile time
	}
	
	public static function compile_sass($force = FALSE)
	{
		// compile time
	}
	
}