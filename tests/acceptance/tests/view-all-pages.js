var expect = require('chai').expect,
	assert = require("assert"),
	Browser = require('zombie');

var browser = new Browser({
	site: "http://localhost/"
});

visit_static_page("stats", browser);
visit_static_page("admin", browser);
//visit_static_page("profile", browser);
visit_static_page("passwordreset", browser);
visit_static_page("test", browser);
visit_static_page("about", browser);
visit_static_page("cat", browser);
visit_static_page("forum", browser);
visit_static_page("search", browser);
visit_static_page("minimumskills", browser);
visit_static_page("question", browser);

function visit_static_page(pageURI, browser) {
	describe("Given I visit the " + pageURI + " page", function (done) {

		before(function (done) {
			browser.visit('/' + pageURI, done);
		});

		it("The page loads fine", function () {
			expect(browser.statusCode).to.equal(200);
		});
	});
}