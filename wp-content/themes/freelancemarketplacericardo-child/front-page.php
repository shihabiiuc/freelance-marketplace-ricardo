<?php
/**
 * Created by PhpStorm.
 * User: Shihab
 * Date: 24/07/2018
 * Time: 16:57
 * Template Name: Ricardo Home Page
 */
get_header();
global $user_ID;
?>

<!--Project categories nav-->
<?php
$terms = get_terms('project_category', array(
	'orderby'   => 'name',
	'order'     => 'ASC',
	'hide_empty' => true,
	'number' => 6,
)); ?>

<nav id="catNav" class="navbar navbar-default">
    <div class="container-fluid">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand visible-xs" href="#">Top Categories</a>
        </div>


        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">

                <?php
                    foreach ($terms as $term) {
                        $term_link = get_term_link($term);
                        if (is_wp_error($term_link)) {
                            continue;
                        }
                        echo '<li><a href="' . esc_url($term_link) . '">' . $term->name . '</a></li>';
                    }
				?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo site_url('/project_category/web-search/'); ?>">View All Categories</a></li>
            </ul>
        </div>

    </div>
</nav>
<!--Project categories nav end-->

<!--Home page banner-->
<section class="ricardobanner container-fluid">
    <div class="row">

        <div class="col-xs-12 col-sm-6 col-md-6 ricardobanner__leftcontent">
            <h1 class="ricardobanner__leftcontent__heading">Get it done with a<br>freelancer</h1>
            <p class="ricardobanner__leftcontent__subtitle">Grow your business with the top freelancing website.</p>
            <a class="ricardobanner__leftcontent__getstarted" href="<?php echo site_url('/register'); ?>">Get Started</a>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-6 ricardobanner__middlecontent">
            <img src="<?php echo get_theme_file_uri('/images/Roger-Gardner.png'); ?>" alt="">
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 ricardobanner__rightcontent">
            <p><b>Roger Gardner</b></p>
            <p>CEO, Learfield</p>
            <a id="youtubeAutoplayToggle" href="#" data-toggle="modal" data-target="#story"><i class="fab fa-youtube"></i> Watch his story</a>
        </div>

    </div>
</section>

<!--Modal story-->
<div class="modal fade" id="story">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="h5 modal-title">Story</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span><i class="fas fa-window-close"></i></span>
                </button>
            </div>

            <div class="modal-body" id="videoWidthHeight">
                <iframe src="" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>
<!--Modal story end -->
<!--Home page banner end -->

<!--Trusted by start-->
<section class="container-fluid trusted js-ini-position">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-2"><p>Trusted by hundreds of businessmen &amp; organizations</p></div>

            <div class="col-xs-6 col-sm-2">
                <img src="<?php echo get_theme_file_uri('/images/danish.png'); ?>" alt="client">
            </div>
            <div class="col-xs-6 col-sm-2">
                <img src="<?php echo get_theme_file_uri('/images/persona.png'); ?>" alt="client">
            </div>
            <div class="col-xs-6 col-sm-2">
                <img src="<?php echo get_theme_file_uri('/images/apex.png'); ?>" alt="client">
            </div>
            <div class="col-xs-6 col-sm-2">
                <img src="<?php echo get_theme_file_uri('/images/Arla_Foods.png'); ?>" alt="client">
            </div>
            <div class="col-xs-12 col-sm-2">
                <img src="<?php echo get_theme_file_uri('/images/Walton.png'); ?>" alt="client">
            </div>
        </div>
    </div>
</section>
<!--Trusted by end-->

<!--Hire for any scope of work-->
<section class="container-fluid scopeofwork">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <p class="scopeofwork__heading">You can hire for any scope<br>of project</p>
            </div>
            <div class="col-md-3 scopeofwork__border">
                <p class="scopeofwork__title"><i class="fas fa-compass"></i> Short-term tasks</p>
                <p class="scopeofwork__description">You can hire our talented freelancers for any short-term project</p>
            </div>
            <div class="col-md-3 scopeofwork__border">
                <p class="scopeofwork__title"><i class="fas fa-cubes"></i> Recurring projects</p>
                <p class="scopeofwork__description">If you need help frequently, you can have a go-to team with specialized skills.</p>
            </div>
            <div class="col-md-3 scopeofwork__border">
                <p class="scopeofwork__title"><i class="fas fa-handshake"></i> Full-time contract work</p>
                <p class="scopeofwork__description">Expand your staff with a dedicated team and have a outsource from our marketplace.</p>
            </div>
        </div>
    </div>
</section>
<!--Hire for any scope of work end-->




<!-- Block Banner -->
<div class="fre-background" id="background_banner"
     style="background-image: url('<?php echo get_theme_mod("background_banner") ? get_theme_mod("background_banner") : get_template_directory_uri() . "/img/fre-bg.png"; ?>');">
    <div class="fre-bg-content">
        <div class="container">
            <h1 id="title_banner"><?php echo get_theme_mod("title_banner") ? get_theme_mod("title_banner") : __("Find perfect freelancers for your projects or Look for freelance jobs online?", ET_DOMAIN); ?></h1>
			<?php if (!is_user_logged_in()) { ?>
				<?php if (!fre_check_register()) { ?>
                    <a class="fre-btn primary-bg-color"
                       href="<?php echo get_post_type_archive_link(PROFILE); ?>"><?php _e('Find Freelancers', ET_DOMAIN); ?></a>
                    <a class="fre-btn primary-bg-color"
                       href="<?php echo get_post_type_archive_link(PROJECT); ?>"><?php _e('Find Projects', ET_DOMAIN); ?></a>
				<?php } else { ?>
                    <a class="fre-btn primary-bg-color"
                       href="<?php echo et_get_page_link('register', array("role" => 'employer')); ?>"><?php _e('Hire Freelancer', ET_DOMAIN); ?></a>
                    <a class="fre-btn primary-bg-color"
                       href="<?php echo et_get_page_link('register', array("role" => 'freelancer')); ?>"><?php _e('Apply as Freelancer', ET_DOMAIN); ?></a>
				<?php } ?>

			<?php } else { ?>
				<?php if (ae_user_role($user_ID) == FREELANCER) { ?>
                    <a class="fre-btn primary-bg-color"
                       href="<?php echo get_post_type_archive_link(PROJECT); ?>"><?php _e('Find Projects', ET_DOMAIN); ?></a>
                    <a class="fre-btn primary-bg-color"
                       href="<?php echo et_get_page_link('profile'); ?>"><?php _e('Update Profile', ET_DOMAIN); ?></a>
				<?php } else { ?>
                    <a class="fre-btn primary-bg-color"
                       href="<?php echo et_get_page_link('submit-project'); ?>"><?php _e('Post a Project', ET_DOMAIN); ?></a>
                    <a class="fre-btn primary-bg-color"
                       href="<?php echo get_post_type_archive_link(PROFILE); ?>"><?php _e('Find Freelancers', ET_DOMAIN); ?></a>
				<?php } ?>
			<?php } ?>
        </div>
    </div>
</div>
<!-- Block Banner -->
<!-- Block How Work -->
<div class="fre-how-work">
    <div class="container">
        <h2 id="title_work"><?php echo get_theme_mod("title_work") ? get_theme_mod("title_work") : __('How FreelanceEngine works?', ET_DOMAIN); ?></h2>
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="fre-work-block">
					<span>
						<img src="<?php echo get_theme_mod('img_work_1') ? get_theme_mod('img_work_1') : get_template_directory_uri() . '/img/1.png'; ?>"
                             id="img_work_1" alt="">
					</span>
                    <p id="desc_work_1"><?php echo get_theme_mod("desc_work_1") ? get_theme_mod("desc_work_1") : __('Post projects to tell us what you need done', ET_DOMAIN); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="fre-work-block">
					<span>
						<img src="<?php echo get_theme_mod('img_work_2') ? get_theme_mod('img_work_2') : get_template_directory_uri() . '/img/2.png'; ?>"
                             id="img_work_2" alt="">
					</span>
                    <p id="desc_work_2"><?php echo get_theme_mod("desc_work_2") ? get_theme_mod("desc_work_2") : __('Browse profiles, reviews, then hire your most favorite and start project', ET_DOMAIN); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="fre-work-block">
					<span>
						<img src="<?php echo get_theme_mod('img_work_3') ? get_theme_mod('img_work_3') : get_template_directory_uri() . '/img/3.png'; ?>"
                             id="img_work_3" alt="">
					</span>
                    <p id="desc_work_3"><?php echo get_theme_mod("desc_work_3") ? get_theme_mod("desc_work_3") : __('Use FreelanceEngine platform to chat and share files', ET_DOMAIN); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="fre-work-block">
					<span>
						<img src="<?php echo get_theme_mod('img_work_4') ? get_theme_mod('img_work_4') : get_template_directory_uri() . '/img/4.png'; ?>"
                             id="img_work_4" alt="">
					</span>
                    <p id="desc_work_4"><?php echo get_theme_mod("desc_work_4") ? get_theme_mod("desc_work_4") : __('With our protection, money is only paid for work you authorize', ET_DOMAIN); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Block How Work -->
<!-- List Profiles -->
<div class="fre-perfect-freelancer">
    <div class="container">
        <h2 id="title_freelance"><?php echo get_theme_mod("title_freelance") ? get_theme_mod("title_freelance") : __('Find perfect freelancers for your projects', ET_DOMAIN); ?></h2>
		<?php get_template_part('home-list', 'profiles'); ?>
        <div class="fre-perfect-freelancer-more">
            <a class="fre-btn-o primary-color"
               href="<?php echo get_post_type_archive_link(PROFILE); ?>"><?php _e('See all freelancers', ET_DOMAIN); ?></a>
        </div>
    </div>
</div>
<!-- List Profiles -->
<!-- List Projects -->
<div class="fre-jobs-online">
    <div class="container">
        <h2 id="title_project"><?php echo get_theme_mod("title_project") ? get_theme_mod("title_project") : __('Browse numerous freelance jobs online', ET_DOMAIN); ?></h2>
		<?php get_template_part('home-list', 'projects'); ?>
    </div>
</div>
<!-- List Projects -->
<!-- List Testimonials -->
<div class="fre-our-stories">
    <div class="container">
        <h2 id="title_story"><?php echo get_theme_mod("title_story") ? get_theme_mod("title_story") : __('Hear what our customers have to say', ET_DOMAIN); ?></h2>
		<?php get_template_part('home-list', 'testimonial'); ?>
    </div>
</div>
<!-- List Testimonials -->
<!-- List Pricing Plan -->
<?php
global $disable_plan, $pay_to_bid;
$disable_plan = (int)ae_get_option('disable_plan', false);
$pay_to_bid   = ae_get_option('pay_to_bid', false);

if (!$disable_plan || $pay_to_bid) { ?>
    <div class="fre-service">
        <div class="container">
            <h2 id="title_service">
				<?php
				if (ae_user_role($user_ID) == FREELANCER) {
					echo get_theme_mod("title_service_freelancer") ? get_theme_mod("title_service_freelancer") : __('Select the level of service you need for project bidding', ET_DOMAIN);
				} else {
					echo get_theme_mod("title_service") ? get_theme_mod("title_service") : __('Select the level of service you need for project posting', ET_DOMAIN);
				}
				?>
            </h2>
			<?php get_template_part('home-list', 'pack'); ?>
        </div>
    </div>
<?php } ?>
<!-- List Pricing Plan -->
<!-- List Get Started -->
<div class="fre-get-started">
    <div class="container">
        <div class="get-started-content">
			<?php if (!is_user_logged_in()) { ?>
                <h2 id="title_start"><?php echo get_theme_mod("title_start") ? get_theme_mod("title_start") : __('Need work done? Join FreelanceEngine community!', ET_DOMAIN); ?></h2>
				<?php if (fre_check_register()) { ?>
                    <a class="fre-btn fre-btn primary-bg-color"
                       href="<?php echo et_get_page_link('register'); ?>"><?php _e('Get Started', ET_DOMAIN) ?></a>
				<?php } ?>
			<?php } else { ?>
				<?php if (ae_user_role($user_ID) == FREELANCER) { ?>
                    <h2 id="title_start"><?php echo get_theme_mod("title_start_freelancer") ? get_theme_mod("title_start_freelancer") : __("It's time to start finding freelance jobs online!", ET_DOMAIN); ?></h2>
                    <a class="fre-btn fre-btn primary-bg-color"
                       href="<?php echo get_post_type_archive_link(PROJECT); ?>"><?php _e('Find Projects', ET_DOMAIN) ?></a>
				<?php } else { ?>
                    <h2 id="title_start"><?php echo get_theme_mod("title_start_employer") ? get_theme_mod("title_start_employer") : __('The best way to find perfect freelancers!', ET_DOMAIN); ?></h2>
                    <a class="fre-btn fre-btn primary-bg-color"
                       href="<?php echo et_get_page_link('submit-project'); ?>"><?php _e('Post a Project', ET_DOMAIN) ?></a>
				<?php } ?>
			<?php } ?>

        </div>
    </div>
</div>
<!-- List Get Started -->
<?php get_footer(); ?>
