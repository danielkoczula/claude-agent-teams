import { store, getContext } from '@wordpress/interactivity';

const { state } = store( 'wlc/portfolio-grid', {
	state: {
		get filteredPosts() {
			const { posts, activeCategory, activeTech } = state;
			return posts.filter( post => {
				const catOk = ! activeCategory || post.categorySlugs.includes( activeCategory );
				const techOk = ! activeTech || post.techSlugs.includes( activeTech );
				return catOk && techOk;
			} );
		},
		get totalPages() {
			return Math.max( 1, Math.ceil( state.filteredPosts.length / state.postsPerPage ) );
		},
		get isFirstPage() {
			return state.currentPage <= 1;
		},
		get isLastPage() {
			return state.currentPage >= state.totalPages;
		},
		get hasPagination() {
			return state.totalPages > 1;
		},
		get hasVisiblePosts() {
			return state.filteredPosts.length > 0;
		},
		// Filter buttons — reads filterValue from data-wp-context on each button
		get isCategoryActive() {
			const context = getContext();
			return state.activeCategory === ( context.filterValue ?? '' );
		},
		get isTechActive() {
			const context = getContext();
			return state.activeTech === ( context.filterValue ?? '' );
		},
		// Cards — reads postId/categorySlugs/techSlugs from data-wp-context on each article
		get isPostVisible() {
			const context = getContext();
			const { categorySlugs, techSlugs, postId } = context;
			const { activeCategory, activeTech, currentPage, postsPerPage, filteredPosts } = state;

			const catOk = ! activeCategory || ( categorySlugs && categorySlugs.includes( activeCategory ) );
			const techOk = ! activeTech || ( techSlugs && techSlugs.includes( activeTech ) );
			if ( ! catOk || ! techOk ) return false;

			const index = filteredPosts.findIndex( p => p.id === postId );
			if ( index === -1 ) return false;
			const start = ( currentPage - 1 ) * postsPerPage;
			const end = start + postsPerPage;
			return index >= start && index < end;
		},
		// Pagination buttons — reads pageNum from data-wp-context on each button
		get isCurrentPage() {
			const context = getContext();
			return state.currentPage === context.pageNum;
		},
		get isPageHidden() {
			const context = getContext();
			return context.pageNum > state.totalPages;
		},
	},
	actions: {
		filterByCategory() {
			const { filterValue } = getContext();
			state.activeCategory = filterValue ?? '';
			state.currentPage = 1;
		},
		filterByTech() {
			const { filterValue } = getContext();
			state.activeTech = filterValue ?? '';
			state.currentPage = 1;
		},
		prevPage() {
			if ( ! state.isFirstPage ) {
				state.currentPage -= 1;
			}
		},
		nextPage() {
			if ( ! state.isLastPage ) {
				state.currentPage += 1;
			}
		},
		goToPage() {
			const { pageNum } = getContext();
			if ( pageNum && pageNum >= 1 && pageNum <= state.totalPages ) {
				state.currentPage = pageNum;
			}
		},
	},
} );
