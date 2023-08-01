<div id="dynamic-post"></div>
<script type="text/template" id="package-template">
    <div class="h-screen pt-[100px] pt-[100px] relative px-6 pb-12 flex flex-col justify-end">
		<img
				src="<%= featured_image %>"
				class="z-[-1] absolute top-0 h-screen object-cover w-full right-0"
				alt=""
		/>
		<div aria-hidden="true" class="absolute inset-0 z-[-1] bg-gradient-b-to-t "></div>

	    <% if (video ) { %>
		<div class="vimeo-wrapper absolute ">
			<iframe src="https://player.vimeo.com/video/830266264?h=db57139e05&badge=0&autopause=0&player_id=0&background=1&muted=1"
			        frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe><br />

		</div>
		<% } %>
		<div class="max-w-[450px]">
			<h1 class="text-5xl font-medium mb-7 leading-[1.1]"><%= title %></h1>
		</div>

	    <% if (duration || length || price) { %>
		<ul class="mb-6">
			<% if (duration) { %>
			<li class="inline-flex py-2 px-5 flex-col text-lg font-semibold bg-primary/80 justify-center border border-white rounded-lg mr-4">
				<span class="text-xs">Durée</span><%= duration %>
			</li>
			<% } %>

			 <% if (length) { %>
			<li class="inline-flex py-2 px-4 flex-col text-lg font-semibold justify-center bg-primary/80 border border-white rounded-lg mr-4">
				<span class="text-xs">Temps de vol</span><%= length %>
			</li>
			<% } %>

			 <% if (price) { %>
				<li class="inline-flex py-2 px-4 flex-col text-lg font-semibold bg-foreground-light/80 justify-center border border-white rounded-lg mr-4">
					<span class="text-xs">Prix&nbsp;/&nbsp;personne</span><%= price %>$
				</li>
			<% } %>
		</ul>
		<% } %>

	    <% if (Object.keys(slides).length > 0) { %>
	    <div class="pb-[205px]"></div>
		    <div id="package-slider" class="swiper swiper-container-free-mode absolute  right-[1.5rem] left-[1.5rem] max-h-[205px]">
				<div class="swiper-wrapper">
					 <% for (let key in slides) { %>
		                <div class="wp-block-custom-slide swiper-slide w-full max-w-[390px]">
					        <div class="shadow-lg magnetic group aspect-h-1 aspect-w-[1.91] block w-full overflow-hidden rounded-lg focus-within:ring-2 focus-within:ring-secondary-500 focus-within:ring-offset-2 focus-within:ring-offset-gray-100">
					            <img src="<%= slides[key].featured_image %>" alt="" class="object-cover">
					            <a href="<%= slides[key].permalink %>"
					               class="absolute inset-0 z-1 bg-gradient-b-to-t group-hover:opacity-50 transition-all"></a>
					            <h2 class="max-w-[75%] leading-6 absolute pointer-events-none p-4 md:p-6 z-[2] text-lg sm:text-2xl font-bold tracking-tight text-white ">
					                <%= slides[key].title %>
					            </h2>
					        </div>
					    </div>
		            <% }; %>
				</div>
			</div>
	    <% } %>
	</div>
	<div class="bg-white text-black">
		<div class="max-w-[1536px] mx-auto px-6 py-12">
			<%= content %>
		</div>
	</div>
</script>


