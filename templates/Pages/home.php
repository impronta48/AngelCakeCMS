<h1>AngelCake CMS</h1>
<big>The first CakePHP based Hybrid CMS </big>
<hr>
<h2>Top Features</h2>
<ul>
	<li>CakePHP 3.8 based</li>
	<li>User Management by CakeDC Users Plugins</li>
	<li>Tags Management by CakeDC Plugins</li>
	<li>Static Pages based on Markdown (Jekyl Like style)</li>
	<li>Dynamic Pages based on Web Editing</li>
	<li>Events Registration</li>
	<li>Participant Lists Export in XLS</li>
	<li>Contact Form with HoneyPot (Antispam)</li>
	<li>Sitemap.XML generation</li>
	<li>Multi-Site Management</li>
	<li>Easy Migration from Drupal 6 and 7</li>
</ul>

<h2>Why Another CMS</h2>
<p>We have a long experience in Drupal 4-5-6-7. When Drupal reached version 8, we have made some sites and then decided to abandon our beloved CMS (too much complexity, too much time to create even the simplest things).</p>
<p>We have explored several CMS (first of all WordPress), getting several inspirations, but never being satisfied either of the learning curve, or by the heaviness of the tool.</p>
<p>We explored <a href="https://jekyllrb.com/" target="_blank">Jekyll</a> (and it's cousins written in Node - <a href="https://assemble.io/" target="_blank">Assemble</a>, <a href="https://metalsmith.io/" target="_blank">Metalsmith</a>), but a 100% <a href="https://www.staticgen.com/" target="_blank">static CMS</a> is not what we are looking for: sometimes the customers want a easy interface to insert contents. Sometimes we need the power of a full markdown structure. So we have created a <b>hybrid-cms</b>.</p>
<p>We have used <a href="http://picocms.org/" target="_blank">Pico</a>, but the features its offers are too simple for our use-case.</p>
<p>We have tried <a href="https://croogo.org/" target="_blank">Croogo</a>, but we found the same problems of more widespread CMS.</p>
<p>We love <a href="http://cakephp.org" target="_blank">CakePHP</a> and we have a long experience with it, so we ended up writing something by ourselves. This is not something that we wanted to...</p>

<h2>Hybrid?</h2>
<p>Hybrid = Static & Dynamic at the same time, depending on the feature.</p>
<p>We say hybrid because AngelCake can work both as "web based CMS" (where the users enter their content in a WYSIWYG form) and in a "static file" way (where the users put their MD files in a folder).</p>
<p>Depending on the type of users, the level of personalization required, one solution can be better than the other.</p>

<h2>No Menu/Block/Views/Sliders form the user interface</h2>
<p>This is the most shocking choice.</p>
<p>The user will not change the user experience, but only insert contents. The end-user will call us when they need some deeper modifications. (this is actually what happens with 99% of our customers).</p>
<p>We do not want to invest in complex menu building systems, that work for 90% of the cases, where we loose a lot of time to edit a single icon in the menu, where it would take 5 minutes to edit it in HTML. All the structured modifications are done by a programmer in standard CakePHP elements/views (ie:standard HTML/CSS/PHP).</p>

<h2>No templating layers</h2>
<p>We excluded several CMS because they wanted us to use some layers of templating (like TWIG). </p>
<p>We want a plain HTML/PHP/CSS templating engine. No need to compile. A good cache or a CDN will take care of the speed.</p>
<p>Of course you can add your favourite JS engine on top of AngelCake CMS (we like VUE.js)</p>
