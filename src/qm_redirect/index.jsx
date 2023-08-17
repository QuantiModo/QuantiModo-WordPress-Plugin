function register_qm_redirect_block() {
	console.log( 'Quantimodo iFrame block loaded' );
	const { registerBlockType } = wp.blocks;
	const { ServerSideRender } = wp.editor;

	registerBlockType( 'quantimodo/qm_redirect', {
		title: 'QuantiModo Redirect',
		icon: 'external',
		category: 'common',
		attributes: {},
		edit: function ( props ) {
			return (
				<div>
					<ServerSideRender
						block="quantimodo/qm_redirect"
						attributes={ props.attributes }
					/>
					<h1>Block That Redirects to Your QuantiModo App</h1>
				</div>
			);
		},
		save: function () {
			return null;
		},
	} );
}

wp.domReady( register_qm_redirect_block );
