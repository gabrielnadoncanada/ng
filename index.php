<?php get_header() ?>
<h1 class="screen-reader-text"><?php the_title() ?></h1>

<div class="grid grid-cols-1 gap-x-4 gap-y-8 xs:grid-cols-2 lg:grid-cols-3 sm:gap-x-6 xl:gap-x-8 max-w-[1536px] py-[100px] w-full px-6 mx-auto">
	<?php if (have_posts()):
		while (have_posts()):
			the_post();
			get_template_part("template-parts/content", get_post_type());
		endwhile;
	else:
		get_template_part("template-parts/content", "none");
	endif; ?>
</div>

<?php
get_footer();
?>
