var expect = require('chai').expect,
	assert = require("assert"),
	Browser = require('zombie');

var browser = new Browser({
	site: "http://localhost/"
});

visit_static_page("about", browser);

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