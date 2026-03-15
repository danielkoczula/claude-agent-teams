import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, __experimentalNumberControl as NumberControl, CheckboxControl, Placeholder, Spinner } from '@wordpress/components';
import { useEntityRecords } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';
import { grid } from '@wordpress/icons';

export default function Edit( { attributes, setAttributes } ) {
	const { postsPerPage, showCategoryFilter, showTechnologyFilter } = attributes;

	const blockProps = useBlockProps( { className: 'wlc-portfolio-grid' } );

	const { records: posts, isResolving } = useEntityRecords( 'postType', 'wlc-portfolio', {
		per_page: 3,
		status: 'publish',
		_embed: true,
	} );

	const { records: categories } = useEntityRecords( 'taxonomy', 'portfolio-category', {
		per_page: 10,
		hide_empty: false,
	} );

	const { records: technologies } = useEntityRecords( 'taxonomy', 'technology', {
		per_page: 10,
		hide_empty: false,
	} );

	function renderPreviewCard( post ) {
		const thumbnail = post?._embedded?.[ 'wp:featuredmedia' ]?.[ 0 ]?.source_url;
		const postCategories = post?._embedded?.[ 'wp:term' ]?.flat()?.filter(
			t => t.taxonomy === 'portfolio-category'
		) ?? [];
		const postTechs = post?._embedded?.[ 'wp:term' ]?.flat()?.filter(
			t => t.taxonomy === 'technology'
		) ?? [];

		return (
			<article key={ post.id } className="wlc-portfolio-card">
				<div className="wlc-portfolio-card__link">
					<div className="wlc-portfolio-card__image-wrap">
						{ thumbnail ? (
							<img
								src={ thumbnail }
								alt=""
								aria-hidden="true"
								className="wlc-portfolio-card__image"
							/>
						) : (
							<div className="wlc-portfolio-card__image-placeholder" aria-hidden="true"></div>
						) }
					</div>
					<div className="wlc-portfolio-card__content">
						<div className="wlc-portfolio-card__tags">
							{ postTechs.map( t => (
								<span key={ t.id } className="wlc-portfolio-card__tag wlc-portfolio-card__tag--tech">{ t.name }</span>
							) ) }
							{ postCategories.map( c => (
								<span key={ c.id } className="wlc-portfolio-card__tag wlc-portfolio-card__tag--category">{ c.name }</span>
							) ) }
						</div>
						<h3 className="wlc-portfolio-card__title">{ post.title?.rendered }</h3>
						{ post.excerpt?.rendered && (
							<p className="wlc-portfolio-card__excerpt"
								dangerouslySetInnerHTML={ { __html: post.excerpt.rendered } }
							/>
						) }
						<span className="wlc-portfolio-card__readmore" aria-hidden="true">{ __( 'Read more →', 'wlc-blocks' ) }</span>
					</div>
				</div>
			</article>
		);
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Ustawienia siatki', 'wlc-blocks' ) }>
					<NumberControl
						label={ __( 'Kart na stronę', 'wlc-blocks' ) }
						value={ postsPerPage }
						min={ 1 }
						max={ 50 }
						onChange={ value => setAttributes( { postsPerPage: Number( value ) } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Filtry', 'wlc-blocks' ) }>
					<CheckboxControl
						label={ __( 'Pokaż filtr Kategoria', 'wlc-blocks' ) }
						checked={ showCategoryFilter }
						onChange={ value => setAttributes( { showCategoryFilter: value } ) }
					/>
					<CheckboxControl
						label={ __( 'Pokaż filtr Technologia', 'wlc-blocks' ) }
						checked={ showTechnologyFilter }
						onChange={ value => setAttributes( { showTechnologyFilter: value } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				{ showCategoryFilter && categories?.length > 0 && (
					<div className="wlc-portfolio-grid__filters">
						<div className="wlc-portfolio-grid__filter-row">
							<span className="wlc-portfolio-grid__filter-label">{ __( 'Kategoria:', 'wlc-blocks' ) }</span>
							<div className="wlc-portfolio-grid__filter-buttons">
								<button className="wlc-portfolio-grid__filter-btn wlc-portfolio-grid__filter-btn--active">
									{ __( 'Wszystkie', 'wlc-blocks' ) }
								</button>
								{ categories.map( cat => (
									<button key={ cat.id } className="wlc-portfolio-grid__filter-btn">{ cat.name }</button>
								) ) }
							</div>
						</div>
					</div>
				) }

				{ showTechnologyFilter && technologies?.length > 0 && (
					<div className="wlc-portfolio-grid__filters wlc-portfolio-grid__filters--tech">
						<div className="wlc-portfolio-grid__filter-row">
							<span className="wlc-portfolio-grid__filter-label">{ __( 'Technologia:', 'wlc-blocks' ) }</span>
							<div className="wlc-portfolio-grid__filter-buttons">
								<button className="wlc-portfolio-grid__filter-btn wlc-portfolio-grid__filter-btn--active">
									{ __( 'Wszystkie', 'wlc-blocks' ) }
								</button>
								{ technologies.map( tech => (
									<button key={ tech.id } className="wlc-portfolio-grid__filter-btn">{ tech.name }</button>
								) ) }
							</div>
						</div>
					</div>
				) }

				{ ( showCategoryFilter || showTechnologyFilter ) && (
					<div className="wlc-portfolio-grid__divider" aria-hidden="true"></div>
				) }

				{ isResolving && (
					<div style={ { textAlign: 'center', padding: '2rem' } }>
						<Spinner />
					</div>
				) }

				{ ! isResolving && ( ! posts || posts.length === 0 ) && (
					<Placeholder
						icon={ grid }
						label={ __( 'WLC Portfolio Grid', 'wlc-blocks' ) }
						instructions={ __( 'Dodaj wpisy portfolio żeby zobaczyć podgląd.', 'wlc-blocks' ) }
					/>
				) }

				{ ! isResolving && posts && posts.length > 0 && (
					<div className="wlc-portfolio-grid__grid">
						{ posts.map( renderPreviewCard ) }
					</div>
				) }
			</section>
		</>
	);
}
