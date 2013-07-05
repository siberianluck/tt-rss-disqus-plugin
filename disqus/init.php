<?php
class Disqus extends Plugin {
	private $host;

    function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

	function about() {
		return array(1.0,
			"Integrate disqus comments",
			"ben");
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_BUTTON, $this);
		$host->add_hook($host::HOOK_RENDER_ARTICLE, $this);
		$host->add_hook($host::HOOK_RENDER_ARTICLE_CDM, $this);
	}

	function get_js() {
		return file_get_contents(dirname(__FILE__) . "/init.js");
	}

	function hook_article_button($line) {
        if (strpos($line["link"], "?") !== FALSE) {
            $link = preg_replace('/^(.+)#comment.*/', '$1', $line['link']);
            $uuid = preg_replace('/.*key=(.+)#comment.*/', '$1', $line['link']);
        } else {
            $result = db_query("SELECT uuid, ref_id FROM ttrss_user_entries WHERE int_id = '{$line['int_id']}' AND owner_uid = " . $_SESSION['uid']);
            $uuid = db_fetch_result($result, 0, "uuid");
            if (!$uuid) {
                    $uuid = db_escape_string(sha1(uniqid(rand(), true)));
                    db_query("UPDATE ttrss_user_entries SET uuid = '$uuid' WHERE int_id = '{$line['int_id']}'
                        AND owner_uid = " . $_SESSION['uid']);
            }
            $link = get_self_url_prefix();
            $link .= "/public.php?op=share&key=$uuid";
        }
        $title = str_replace('"', "'", $line['title']);
        $title = str_replace("'", "\'", $title);
		return "<a class=\"comment-count\"
                href=\"#disqus_thread\"
                data-disqus-identifier=\"{$uuid}\"
					onclick=\"disqusArticle(".$line["id"].", '$link', '$uuid', '".$title."')\"
					>Comments</a>";
	}

    function hook_render_article($article) {
        if (strpos($article["link"], "?") !== FALSE) {

            $html = $this->file_get_contents_curl($article['link']);

            //parsing begins here:
            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $xpath = new DomXpath($doc);
            $node = $xpath->query('//*[@class="postContent"]')->item(0);
            $innerHTML = "";
            $children = $node->childNodes;
            foreach($children as $child) {
                $tmp_dom = new DOMDocument();
                $tmp_dom->appendChild($tmp_dom->importNode($child, true));
                $innerHTML.=trim($tmp_dom->saveHTML());
            }

            //get and display what you need:
            $article['content'] = '<div class="shared-article-note">' . $article['content'] . '</div>' . $innerHTML;
        }
        return $article;
    }

    function hook_render_article_cdm($article) {
        if (strpos($article["link"], "?") !== FALSE) {

            $html = $this->file_get_contents_curl($article['link']);

            //parsing begins here:
            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $xpath = new DomXpath($doc);
            $node = $xpath->query('//*[@class="postContent"]')->item(0);
            $innerHTML = "";
            $children = $node->childNodes;
            foreach($children as $child) {
                $tmp_dom = new DOMDocument();
                $tmp_dom->appendChild($tmp_dom->importNode($child, true));
                $innerHTML.=trim($tmp_dom->saveHTML());
            }

            //get and display what you need:
            $article['content'] = '<div class="shared-article-note">' . $article['content'] . '</div>' . $innerHTML;
        }
        return $article;
    }

	function api_version() {
		return 2;
	}


}
?>
