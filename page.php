<?php get_header() ?>
<h1 class="screen-reader-text"><?php the_title() ?></h1>

<?php

while (have_posts()) :
	the_post();
	get_template_part('template-parts/content', 'page');
endwhile;

get_footer()
?>