<?php

/**
 * BuddyPress - Users Home
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'buddypress' ); ?>
<section id="content">
	<div id="buddypress">
	    <div class="container">
	        <div class="row">
	            <div class="col-md-2 col-sm-4">
	             <?php do_action( 'bp_before_member_home_content' ); ?>
	                <div class="pagetitle">
						<div id="item-header" role="complementary">
							<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

						</div><!-- #item-header -->
					</div>
					<div id="item-nav" class="">
						<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
							<ul>

								<?php bp_get_displayed_user_nav(); ?>

								<?php do_action( 'bp_member_options_nav' ); ?>

							</ul>
						</div>
					</div><!-- #item-nav -->
				</div>	
				<div class="col-md-10 col-sm-8">
					<div class="padder">
						<div id="item-body">
							<?php do_action( 'template_notices' ); ?>
							<?php do_action( 'bp_before_member_body' );

							if ( bp_is_user_activity() || !bp_current_component() ) :
								locate_template( array( 'members/single/activity.php'  ), true );

							 elseif ( bp_is_user_blogs() ) :
								locate_template( array( 'members/single/blogs.php'     ), true );

							elseif ( bp_is_user_friends() ) :
								locate_template( array( 'members/single/friends.php'   ), true );

							elseif ( bp_is_user_groups() ) :
								locate_template( array( 'members/single/groups.php'    ), true );

							elseif ( bp_is_user_messages() ) :
								locate_template( array( 'members/single/messages.php'  ), true );

							elseif ( bp_is_user_notifications() ) :
								locate_template( array( 'members/single/notifications.php'  ), true );

							elseif ( bp_is_user_profile() ) :
								locate_template( array( 'members/single/profile.php'   ), true );

							elseif ( bp_is_user_forums() ) :
								locate_template( array( 'members/single/forums.php'    ), true );

							elseif ( bp_is_user_settings() ) :
								locate_template( array( 'members/single/settings.php'  ), true );

							elseif ( bp_is_user_course() ) :
								locate_template( array( 'members/single/course.php'    ), true );

							// If nothing sticks, load a generic template
							else :
								locate_template( array( 'members/single/plugins.php'   ), true );

							endif;

							do_action( 'bp_after_member_body' ); ?>

						</div><!-- #item-body -->

						<?php do_action( 'bp_after_member_home_content' ); ?>

					</div><!-- .padder -->
				</div>
			</div>
		</div>
	</div>
</section><!-- #content -->
</div>
<?php get_footer( 'buddypress' ); ?>
