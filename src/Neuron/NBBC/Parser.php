<?php

require_once ('nbbc_main.php');

class Neuron_NBBC_Parser
{
	public static function parse ($code)
	{
		$bbcode = new BBCode ();
		
		if (defined ('SMILEY_DIR'))
		{
			$bbcode->SetSmileyDir (substr (SMILEY_PATH, 0, -1));
			$bbcode->SetSmileyURL (substr (SMILEY_DIR, 0, -1));
		}
		
		// A few backwards compatible issues
		$code = str_replace ('[img:right]', '[img align="right"]', $code);
		
		/*
			'quote' => Array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => "DoQuote",
				'allow_in' => Array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n<b>Quote:</b>\n",
				'plain_end' => "\n",
			),
		*/
		
		// Open tags
		$bbcode->AddRule
		(
			'open',
			array
			(
				'mode' => BBCODE_MODE_CALLBACK,
				'method' => array (__CLASS__, 'DoOpen'),
				'class' => 'link',
				'allow_in' => Array('listitem', 'block', 'columns', 'inline'),
				'content' => BBCODE_REQUIRED,
				'plain_start' => "<a href=\"{\$link}\">",
				'plain_end' => "</a>",
				'plain_content' => Array('_content', '_default'),
				'plain_link' => Array('_default', '_content'),
                'before_tag' => "sns",
                'after_tag' => "sns",
                'before_endtag' => "sns",
                'after_endtag' => "sns",
			)
		);
		
		$bbcode->AddRule
		(
			'colour',
			array
			(
				'mode' => BBCODE_MODE_ENHANCED,
				'allow' => Array('_default' => '/^#?[a-zA-Z0-9._ -]+$/'),
				'template' => '<span style="color:{$_default/tw}">{$_content/v}</span>',
				'class' => 'inline',
				'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
                'before_tag' => "sns",
                'after_tag' => "sns",
                'before_endtag' => "sns",
                'after_endtag' => "sns",
                'content' => null,
			)
		);
		
		for ($i = 1; $i < 5; $i ++)
		{
			$bbcode->AddRule
			(
				'h'.$i,
				array
				(
					'mode' => BBCODE_MODE_SIMPLE,
					'simple_start' => "\n<h".$i.">\n",
					'simple_end' => "\n</h".$i.">\n",
					'allow_in' => Array('listitem', 'block', 'columns'),
					'before_tag' => "sns",
					'after_tag' => "sns",
					'before_endtag' => "sns",
					'after_endtag' => "sns",
					'plain_start' => "\n",
					'plain_end' => "\n",
                    'content' => null,
				)
			);
		}
		
		$bbcode->AddRule
		(
			'quote',
			array
			(
				'mode' => BBCODE_MODE_CALLBACK,
				'method' => array (__CLASS__, 'DoQuote'),
				'allow_in' => Array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n<b>Quote:</b>\n",
				'plain_end' => "\n",
                'content' => null,
			)
		);
		
		$bbcode->AddRule
		(
			'span',
			array
			(
				'mode' => BBCODE_MODE_CALLBACK,
				'method' => array (__CLASS__, 'DoSpan'),
				'allow_in' => Array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n<b>Quote:</b>\n",
				'plain_end' => "\n",
                'content' => null,
			)
		);
		
		/*
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoURL',
				'class' => 'link',
				'allow_in' => Array('listitem', 'block', 'columns', 'inline'),
				'content' => BBCODE_REQUIRED,
				'plain_start' => "<a href=\"{\$link}\">",
				'plain_end' => "</a>",
				'plain_content' => Array('_content', '_default'),
				'plain_link' => Array('_default', '_content'),
		*/
		
		$bbcode->AddRule
		(
			'action',
			array
			(
				'mode' => BBCODE_MODE_CALLBACK,
				'method' => array (__CLASS__, 'DoAction'),
				'class' => 'link',
				'allow_in' => Array('listitem', 'block', 'columns', 'inline'),
				'content' => BBCODE_REQUIRED,
				'plain_start' => "<a href=\"{\$link}\">",
				'plain_end' => "</a>",
				'plain_content' => Array('_content', '_default'),
				'plain_link' => Array('_default', '_content'),
                'before_tag' => "sns",
                'after_tag' => "sns",
                'before_endtag' => "sns",
                'after_endtag' => "sns",
			)
		);
		
		return '<div class="text">'.@$bbcode->Parse ($code).'</div>';
	}
	
	// Format a [quote] tag.  This tag can come in a variety of flavors:
	//
	//  [quote]...[/quote]
	//  [quote=Tom]...[/quote]
	//  [quote name="Tom"]...[/quote]
	//
	// In the third form, you can also add a date="" parameter to display the date
	// on which Tom wrote it, and you can add a url="" parameter to turn the author's
	// name into a link.  A full example might be:
	//
	//  [quote name="Tom" date="July 4, 1776 3:48 PM" url="http://www.constitution.gov"]...[/quote]
	//
	// The URL only allows http, https, mailto, gopher, ftp, and feed protocols for safety.
	public static function DoQuote($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK) return true;
		
		$text = Neuron_Core_Text::getInstance ();

		$txtname = 'quote';
		$txtparams = array ();

		if (isset($params['name'])) 
		{		
			$txtparams['name'] = htmlspecialchars(trim($params['name']));			
			$txtname .= '_wrote';
			
			if (isset($params['date']))
			{
				$date = strtotime ($params['date']);
				$txtparams['date'] = date (DATETIME, $date);
				
				$txtname .= '_date';
			}
		}
		else if (!is_string($default))
		{
			// Do nothing
		}
		else
		{
			$txtparams['name'] = htmlspecialchars(trim($default));			
			$txtname .= '_wrote';
		}
		
		$title = Neuron_Core_Tools::putIntoText
		(
			$text->get ($txtname, 'textconvert', 'main', ""),
			$txtparams
		);
		
		$out = "\n<blockquote class=\"bbcode_quote\">\n";
		if (!empty ($title))
		{
			$out .= "<h4 class=\"bbcode_quote_head\">" . $title . "</h4>\n";
		}
		$out .= "<div class=\"bbcode_quote_body\">" . $content . "</div>\n</blockquote>\n";
		
		return $out;
	}
	
	public static function DoSpan ($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK) return true;
		
		$span = '';

		if (isset($params['class'])) 
		{		
			$span = $params['class'];
		}
		else if (!is_string($default))
		{
			// Do nothing
		}
		else
		{

		}
		
		$out = '<span class="'.$span.'">' . $content . '</span>';
		
		return $out;
	}
	
	public static function DoAction($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK) return true;
		
		if (!isset ($params['data']))
		{
			return true;
		}
		
		$out = '<a href="javascript:void(0);" onclick="windowAction (this, '.trim ($params['data']).')">'.$content.'</a>';
		
		return $out;
	}
	
	public static function DoOpen ($bbcode, $action, $name, $default, $params, $content)
	{
		// We can't check this with BBCODE_CHECK because we may have no URL before the content
		// has been processed.
		if ($action == BBCODE_CHECK) return true;

		$url = is_string($default) ? $default : $bbcode->UnHTMLEncode(strip_tags($content));
		
		return '<a href="javascript:void(0);" onclick="openWindow(\''.$url.'\');">'.$url.'</a>';
	}
}
?>
