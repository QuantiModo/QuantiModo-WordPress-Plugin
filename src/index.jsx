function qm_register_blocks() {
    console.log('Quantimodo iFrame block loaded');
    const {registerBlockType} = wp.blocks;
    const {ServerSideRender} = wp.editor;
    debugger

    registerBlockType('quantimodo/qm-iframe', {
        title: 'QuantiModo iFrame',
        icon: 'format-gallery',  // Choose an icon: https://developer.wordpress.org/resource/dashicons/
        category: 'common',  // Choose a category: https://developer.wordpress.org/block-editor/developers/block-api/block-registration/#category-optional
        attributes: {
            // Here you can define optional block attributes.
        },
        edit: function (props) {
            return (
                <ServerSideRender
                    block="quantimodo/qm-iframe"
                    attributes={props.attributes}
                />
            );
        },
        save: function () {
            // This block is dynamic, so we save nothing to the post content
            return null;
        },
    });

    registerBlockType('quantimodo/qm-redirect', {
        title: 'QuantiModo Redirect',
        icon: 'external',  // Choose an icon: https://developer.wordpress.org/resource/dashicons/
        category: 'common',  // Choose a category: https://developer.wordpress.org/block-editor/developers/block-api/block-registration/#category-optional
        attributes: {
            // Here you can define optional block attributes.
        },
        edit: function (props) {
            return (
                <div>
                    <ServerSideRender
                        block="quantimodo/qm-redirect"
                        attributes={props.attributes}
                    />
                    <h1>Block That Redirects to Your QuantiModo App</h1>
                </div>
            );
        },
        save: function () {
            // This block is dynamic, so we save nothing to the post content
            return null;
        },
    });
}

wp.domReady(qm_register_blocks)


import { registerBlockType } from '@wordpress/blocks';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';

registerBlockType('petition-the-government/petition-form', {
	title: 'Petition Form',
	icon: 'admin-users',
	category: 'widgets',
	edit: ({ setAttributes, attributes }) => {
		// Implement block editor form with controls if needed
		return (
			<>
				<InspectorControls>
					<PanelBody title="Form Settings">
						{/* Example control: Toggle for displaying organization field */}
						<ToggleControl
							label="Display organization field?"
							checked={attributes.displayOrganization}
							onChange={(val) => setAttributes({ displayOrganization: val })}
						/>
					</PanelBody>
				</InspectorControls>
				<div>
					<p>Petition form will be displayed here.</p>
				</div>
			</>
		);
	},
	save: () => null, // Dynamic block, content generated in PHP
});
