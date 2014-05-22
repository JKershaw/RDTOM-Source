var expect = require('chai').expect,
	assert = require("assert"),
	Browser = require('zombie');

var browser = new Browser({
	site: "http://localhost/html/"
});

describe("Given I visit the home page", function (done) {

	before(function (done) {
		browser.visit('/', done);
	});

	it("Then the page loads", function () {
		expect(browser.text('body')).to.exist;
	});
});