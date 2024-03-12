/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { Component } from '@wordpress/element';

/**
 * Internal dependencies
 */
import metadata from './block.json';

class ButtonBlock extends Component {
	constructor() {
		super( ...arguments );
		this.state = {
			buttonText: 'Click me',
			callbackFunction: '',
		};
	}

	render() {
		const { buttonText, callbackFunction } = this.state;

		return (
			<div>
				<InspectorControls>
					<PanelBody title={ __( 'Button Settings', 'quantimodo' ) }>
						<TextControl
							label={ __( 'Button Text', 'quantimodo' ) }
							value={ buttonText }
							onChange={ ( value ) => this.setState( { buttonText: value } ) }
						/>
						<TextControl
							label={ __( 'Callback Function', 'quantimodo' ) }
							value={ callbackFunction }
							onChange={ ( value ) => this.setState( { callbackFunction: value } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<RichText
					tagName="button"
					value={ buttonText }
					onChange={ ( value ) => this.setState( { buttonText: value } ) }
					onClick={ () => {
						if ( callbackFunction ) {
							eval( callbackFunction );
						}
					} }
				/>
			</div>
		);
	}
}

registerBlockType( metadata.name, {
	title: __( 'Button Block', 'quantimodo' ),
	description: __( 'A button block with custom text and callback function.', 'quantimodo' ),
	category: 'design',
	icon: 'button',
	attributes: {
		buttonText: {
			type: 'string',
			source: 'text',
			selector: 'button',
			default: 'Click me',
		},
		callbackFunction: {
			type: 'string',
			default: '',
		},
	},
	edit: ButtonBlock,
	save: ( props ) => {
		return <RichText.Content { ...props.attributes } tagName="button" />;
	},
} );
