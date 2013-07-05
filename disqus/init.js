PluginHost.register(PluginHost.HOOK_PARAMS_LOADED, function(id){
    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
    disqus_shortname = ''; // required: replace example with your forum shortname
    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function () {
    var s = document.createElement('script'); s.async = true;
    s.type = 'text/javascript';
    s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
    (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
    }());
});

PluginHost.register(PluginHost.HOOK_FEED_LOADED, function(id){
    require(["dojo/ready"], function(ready){
      ready(function(){
        DISQUSWIDGETS.getCount();
      });
    });
});

function disqusArticle(line_id, link, uuid, title) {

    disqus_shortname = ''; // required: replace example with your forum shortname
    disqus_identifier = uuid;
    disqus_url = link;
    disqus_title = title;
    require(["dojo/query"], function(query){
        var old_disqus = query('#disqus_thread');
        dojo.destroy(old_disqus[0]);

        var node = query('div[id="CICD-' + line_id + '"] .cdmFooter');
        node[0].innerHTML = node[0].innerHTML + '<div id="disqus_thread"></div>';
    }
    );

    if (window.DISQUS) {
        DISQUS.reset({
            reload: true,
            config: function () {
                this.page.identifier = disqus_identifier;
                this.page.url = disqus_url;
                this.page.title = disqus_title;
            }
        });
    } else {
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();

    }
    return false;
}



