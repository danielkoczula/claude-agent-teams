import { store, getContext } from '@wordpress/interactivity';
import { actions as routerActions } from '@wordpress/interactivity-router';

const { state } = store( 'wlc/portfolio-grid', {
	actions: {
		*filterByCategory( event ) {
			event.preventDefault();
			const url = new URL( event.target.closest( 'a' ).href );
			yield routerActions.navigate( url.href );
		},
		*filterByTech( event ) {
			event.preventDefault();
			const url = new URL( event.target.closest( 'a' ).href );
			yield routerActions.navigate( url.href );
		},
		*goToPage( event ) {
			event.preventDefault();
			const url = new URL( event.target.closest( 'a' ).href );
			yield routerActions.navigate( url.href );
		},
		*prevPage( event ) {
			event.preventDefault();
			if ( state.currentPage <= 1 ) return;
			const url = new URL( event.target.closest( 'a' ).href );
			yield routerActions.navigate( url.href );
		},
		*nextPage( event ) {
			event.preventDefault();
			if ( state.currentPage >= state.totalPages ) return;
			const url = new URL( event.target.closest( 'a' ).href );
			yield routerActions.navigate( url.href );
		},
	},
} );
