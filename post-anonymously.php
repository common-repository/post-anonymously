<?php
/*
Plugin Name: Post Anonymously
Plugin URI: http://getbusinessblog.com/wordpress-plugins/post-anonymously/
Description: Easy way to allow your site members to post and comment anonymously.
Version: 1.0.2
Author: GetBusinessBlog.com
Author URI: http://getbusinessblog.com/
License: GNU General Public License
*/

/*
	Copyright (C) 2010 - 2015 GetBusinessBlog.com

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.	If not, see <http://www.gnu.org/licenses/>.
*/

add_action( 'in_admin_footer', array('Post_Anonymously_Patch','run') );
register_activation_hook( __FILE__, array('Post_Anonymously_Patch','install') );

class Post_Anonymously_Patch{

	private static $_filePath='';

	public static function install(){
		self::patchFile();
	}

	public static function run(){
		if( isset( $_GET['action'] ) && in_array($_GET['action'],array('do-core-reinstall','do-core-upgrade')) ){
			self::patchFile();
		}
	}

	public static function testFile( $_filePath='' ){
		$_filePath=preg_replace( '@wp-admin@i', '', getcwd() ).$_filePath;
		if(!is_file($_filePath)){
			var_dump('Empty file in '.$_filePath);exit;
		}
		if(!is_readable($_filePath)){
			var_dump('Can\'t  reade file '.$_filePath);exit;
		}
		if(!is_writable($_filePath)){
			var_dump('Can\'t  write in file '.$_filePath);exit;
		}
		$_content=file_get_contents( $_filePath );
		if(empty($_content)){
			var_dump('Empty content in file '.$_filePath);exit;
		}
		self::$_filePath=$_filePath;
		return $_content;
	}
	
	public static function patchFile(){
		$_content = self::testFile( 'wp-includes/comment-template.php' );
		if( !strstr( $_content, 'Post Anonymously' ) ){
			$_content=preg_replace( '@function get_comment_author_email\( \$comment_ID = 0 \) {@i',"function get_comment_author_email( \$comment_ID = 0 ) {
	// Post Anonymously 
	if( get_comment_meta( get_comment( \$comment_ID )->comment_ID, '_anonymous') ){
		return '';
	}
	// /Post Anonymously", $_content );
			$_content=preg_replace( '@function get_comment_author\( \$comment_ID = 0 \) {@i',"function get_comment_author( \$comment_ID = 0 ) {
	// Post Anonymously 
	if( get_comment_meta( get_comment( \$comment_ID )->comment_ID, '_anonymous') ){
		return 'anonymous';
	}
	// /Post Anonymously", $_content );
			$_content=preg_replace( '@function get_comment_author_link\( \$comment_ID = 0 \) {@i',"function get_comment_author_link( \$comment_ID = 0 ) {
	// Post Anonymously 
	if( get_comment_meta( get_comment( \$comment_ID )->comment_ID, '_anonymous') ){
		return 'anonymous';
	}
	// /Post Anonymously", $_content );
			$_content=preg_replace( '@function get_comment_author_IP\( \$comment_ID = 0 \) {@i',"function get_comment_author_IP( \$comment_ID = 0 ) {
	// Post Anonymously 
	if( get_comment_meta( get_comment( \$comment_ID )->comment_ID, '_anonymous') ){
		return '';
	}
	// /Post Anonymously", $_content );
			$_content=preg_replace( '@function get_comment_author_url\( \$comment_ID = 0 \) {@i',"function get_comment_author_url( \$comment_ID = 0 ) {
	// Post Anonymously 
	if( get_comment_meta( get_comment( \$comment_ID )->comment_ID, '_anonymous') ){
		return '';
	}
	// /Post Anonymously", $_content );
			$_content=preg_replace( '@						<p class="form-submit">@i','						<?php if ( is_user_logged_in() ) : ?>
						<!-- Post Anonymously -->
						<p class="comment-form-url"><label for="comment_anonymously">' . __( 'Post Anonymously' ) . '</label>
						<input id="comment_anonymously" name="comment_anonymously" type="checkbox" value="1" /></p>
						<!-- /Post Anonymously -->
						<?php endif; ?>
						<p class="form-submit">',$_content);
	
			file_put_contents( self::$_filePath, $_content );
		}
		$_content = self::testFile( 'wp-comments-post.php' );
		if( !strstr( $_content, 'Post Anonymously' ) ){
			$_content=preg_replace( '@\$comment_id = wp_new_comment\( \$commentdata \);@i','$comment_id = wp_new_comment( \$commentdata );

// Post Anonymously
if( isset($_POST[\'comment_anonymously\']) && $_POST[\'comment_anonymously\'] ){
	add_comment_meta( $comment_id, \'_anonymous\', 1 );
}
// /Post Anonymously',$_content);
			file_put_contents( self::$_filePath, $_content );
		}
		$_content = self::testFile( 'wp-includes/author-template.php' );
		if( !strstr( $_content, 'Post Anonymously' ) ){
			$_content=preg_replace( '@function get_author_posts_url\(\$author_id, \$author_nicename = \'\'\) {@i','function get_author_posts_url($author_id, $author_nicename = \'\') {

// Post Anonymously
	global $post;
	if( get_post_meta( $post->ID, \'_anonymous\' ) ){
		return \'\';
	}
// /Post Anonymously
',$_content);
			$_content=preg_replace( '@function get_the_author_link\(\) {@i','function get_the_author_link() {

// Post Anonymously
	global $post;
	if( get_post_meta( $post->ID, \'_anonymous\' ) ){
		return \'\';
	}
// /Post Anonymously
',$_content);
			$_content=preg_replace( '@function get_the_author\(\$deprecated = \'\'\) {@i','function get_the_author($deprecated = \'\') {

// Post Anonymously
	global $post;
	if( get_post_meta( $post->ID, \'_anonymous\' ) ){
		return \'anonymous\';
	}
// /Post Anonymously
',$_content);
			$_content=preg_replace( '@function get_the_modified_author\(\) {@i','function get_the_modified_author() {

// Post Anonymously
	global $post;
	if( get_post_meta( $post->ID, \'_anonymous\' ) ){
		return \'anonymous\';
	}
// /Post Anonymously
',$_content);
			file_put_contents( self::$_filePath, $_content );
		}
		$_content = self::testFile( 'wp-admin/edit-form-advanced.php' );
		if( !strstr( $_content, 'Post Anonymously' ) ){
			$_content=preg_replace( '@require_once\(\'.\/includes\/meta-boxes.php\'\);@i','require_once(\'./includes/meta-boxes.php\');

// Post Anonymously
add_meta_box("anonim-post", "Post Anonymously", "post_anonym_posting", "post", "normal", "high");
function post_anonym_posting(){
	global $post_ID;
?>
   <label for="anonymous_post">
        <input type="checkbox" <?php if (  get_post_meta( $post_ID, \'_anonymous\' ) ) echo \'checked="checked"\'; ?> id="anonymous_post" name="anonymous_post" /> Post Anonymously
    </label>
<?php
}
// /Post Anonymously
',$_content);
			file_put_contents( self::$_filePath, $_content );
		}
		$_content = self::testFile( 'wp-admin/post.php' );
		if( !strstr( $_content, 'Post Anonymously' ) ){
			$_content=preg_replace( '@switch\(\$action\) {@i','// Post Anonymously
if( isset($_POST[\'anonymous_post\']) && $_POST[\'anonymous_post\'] ){
	add_post_meta( $post_id, \'_anonymous\', 1 );
}
// /Post Anonymously

switch($action) {',$_content);
			file_put_contents( self::$_filePath, $_content );
		}else{
			$_content=str_replace( 'anonumous_post' , 'anonymous_post', $_content );
			file_put_contents( self::$_filePath, $_content );
		}
	}
}