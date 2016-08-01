<?php if (!defined("AT_ROOT")) die('!!!');

class AT_CommentsBuilder{

	public function __construct(){
		add_action( 'comment_form_defaults', array( 'AT_Comments', 'comment_form' ) );
	}

	static public function comment_form( $args ) {
		global $user_identity;

		$commenter = wp_get_current_commenter();
		$req = ( ( get_option( 'require_name_email' ) ) ? ' <span class="required">' .esc_html__( '*', 'atom' ) . '</span> ' : '' );
			
		$fields = array(
			'redirect_to' => ( is_singular( 'portfolio' ) ? '<input type="hidden" name="redirect_to" value="' . AT_Common::portfolio_comment_url() . '" />' : '' ),
			'author' => '<div class="row">
						<div class="col-md-7 col-sm-8">
							<div class="field-text">
								<input type="text" placeholder="' .esc_html__( 'Name*:', 'atom' ) . '" class="form-control"  name="author" id="author" value="' . esc_attr( $commenter['comment_author'] ) . '" size="auto" tabindex="1">
							</div>
						</div>
					</div>',
			'email' => '<div class="row">
						<div class="col-md-7 col-sm-8">
							<div class="field-text">
								<input type="email" placeholder="' .esc_html__( 'Email (will not be published)*:', 'atom' ) . '" class="form-control"  name="email" id="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="auto" tabindex="2" >
							</div>
						</div>
					</div>',
			'url' => '<div class="row">
						<div class="col-md-7 col-sm-8">
							<div class="field-text">
								<input type="text" placeholder="' .esc_html__( 'Website:', 'atom' ) . '" class="form-control"  name="url" id="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="auto" tabindex="3" >
							</div>
						</div>
					</div>',
		);

		$args = array(
			'fields' => apply_filters( 'comment_form_default_fields', $fields ),
			'comment_field' => '<div class="row">
						<div class="col-md-10">
							<div class="tarea tarea-large"><textarea placeholder="' .esc_html__( 'Comment:', 'atom' ) . '" rows="8" cols="5" class="form-control" name="comment" id="comment" cols="60" rows="10" tabindex="4"></textarea></div>
						</div>
					</div>',
			'must_log_in' => '<p class="alert">' . sprintf(esc_html__( 'You must be <a href="%1$s" title="Log in">logged in</a> to post a comment.', 'atom' ), wp_login_url( get_permalink() ) ) . '</p>',
			'logged_in_as' => '<p class="log-in-out">' . sprintf(esc_html__( 'Logged in as <a href="%1$s" title="%2$s">%2$s</a>.', 'atom' ), esc_url(admin_url( 'profile.php' )), esc_attr($user_identity) ) . ' <a href="' . esc_url(wp_logout_url( get_permalink() )) . '" title="' . esc_attr(__( 'Log out of this account', 'atom' )) . '">' .esc_html__( 'Log out &rarr;', 'atom' ) . '</a></p>',
			'comment_notes_before' => '',
			'comment_notes_after' => '',
			'id_form' => 'commentform',
			'id_submit' => 'submit',
			'title_reply' =>esc_html__( 'Leave a Reply', 'atom' ),
			'title_reply_to' =>esc_html__( 'Leave a Reply to %s', 'atom' ),
			'cancel_reply_link' =>esc_html__( 'Click here to cancel reply.', 'atom' ),
			'label_submit' =>esc_html__( 'submit comment', 'atom' ),
		);

		return $args;
	}

	public static function comments_list() {		
		echo apply_filters( 'at_comments_title', '<h2>' . sprintf( _n( '1 Comment', '%1$s Comments', get_comments_number(), 'atom' ), number_format_i18n( get_comments_number() ), get_the_title() ) . '</h2>', array( 'comments_number' => get_comments_number(), 'title' =>  get_the_title() ) );
		echo wp_list_comments( array( 'type' => 'all', 'walker' => new AT_Comments_Walker ) );

		if ( get_option( 'page_comments' ) ) {
			$content .= '<div class="pagination pagination-lg pagination-business">
				' . paginate_comments_links( AT_Common::portfolio_comment_url( $nav = true ) ) . '
			</div>';
		}
	}

}
/** COMMENTS WALKER */
class AT_Comments_Walker extends Walker_Comment{
     
    // init classwide variables
    var $tree_type = 'comment';
    var $db_fields = array( 'parent' => 'comment_parent', 'id' => 'comment_ID' );
 
    /** CONSTRUCTOR
     * You'll have to use this if you plan to get to the top of the comments list, as
     * start_lvl() only goes as high as 1 deep nested comments */
    function __construct() {         
    }
     
    /** START_LVL
     * Starts the list before the CHILD elements are added. */
    function start_lvl( &$output, $depth = 0, $args = array() ) {      
        $GLOBALS['comment_depth'] = $depth + 1;
        $output .= '<div class="hold-comments">';
    }
 
    /** END_LVL
     * Ends the children list of after the elements are added. */
    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $GLOBALS['comment_depth'] = $depth + 1;
        $output .= '</div>';
         
    }
     
    /** START_EL */
    function start_el( &$output, $comment, $depth = 0, $args = array(), $current_object_id = 0 ) {

    	$content = '';
    	if($depth == 0) $content .= '<div class="hold-comments">';

        $depth++;
        $GLOBALS['prev_comment_depth'] = $depth;
        $GLOBALS['comment_depth'] = $depth;
        $GLOBALS['comment'] = $comment;

		$comment_type = get_comment_type( $comment->comment_ID );
		$author = esc_html( get_comment_author( $comment->comment_ID ) );
		$url = esc_url( get_comment_author_url( $comment->comment_ID ) );

		$default_avatar = AT_Common::static_url( 'assets/images/' . ( 'pingback' == $comment_type || 'trackback' == $comment_type )
			? "gravatar_{$comment_type}.png"
			: '/avatars/default-avatar_80.png' );

		$avatar = get_avatar( get_comment_author_email( $comment->comment_ID ), 80);
		if ( $url ) {
			$avatar = '<a href="' . esc_url($url) . '" rel="external nofollow" title="' . esc_attr($author) . '">' . $avatar . '</a>';
			$author = '<a href="' . esc_url($url) . '" rel="external nofollow">' . $author . '</a>';
		}
		$edit_link = get_edit_comment_link( $comment->comment_ID ) ? '<li><a class="link" href="' . esc_url(get_edit_comment_link( $comment->comment_ID )) . '">' .esc_html__( 'Edit', 'atom' ) . '</a></li>' : '';
		$content .=	'<div class="box" id="comment-' . get_comment_ID() . '">
				<figure class="hold-img pull-left">' . $avatar . '</figure>
				<ul class="topiclist list-inline">
					<li><small>Posted by ' . $author . ' on ' . get_comment_date(apply_filters( "at_comment_date_format", 'F d, Y, g:i a' ) ) . '</small></li>
					<li>' . get_comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ) . '</li>
					' . $edit_link . '
				</ul>
				<p>' . get_comment_text( $comment->comment_ID ) . '</p>
				' . ($comment->comment_approved == '0' ? '<p class="alert moderation">' .esc_html_e( 'Your comment is awaiting moderation.', 'atom' ) . '</p>' : '' ) . '
			</div>';

		$output .= $content;
    }
 
    function end_el(&$output, $comment, $depth = 0, $args = array() ) {
    	if ($depth == 0) $output .= '</div>';
    }
     
    /** DESTRUCTOR
     * I'm just using this since we needed to use the constructor to reach the top
     * of the comments list, just seems to balance out nicely:) */
    function __destruct() {
    }
}
