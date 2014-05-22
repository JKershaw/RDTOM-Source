var expect = require('chai').expect,
	assert = require("assert"),
	Browser = require('zombie');

var browser = new Browser({
	site: "http://localhost/"
});

describe("Given I visit the about page", function (done) {

	before(function (done) {
		browser.visit('/about', done);
	});

	it("The page loads fine", function () {
		expect(browser.statusCode).to.equal(200);
	});
});