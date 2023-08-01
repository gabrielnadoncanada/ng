<?php get_header() ?>

<?php

while (have_posts()) :
	the_post();
	get_template_part('template-parts/content-single', get_post_type());
endwhile;

get_sidebar();
get_footer()
?>