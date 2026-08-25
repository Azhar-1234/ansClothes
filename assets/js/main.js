/**
 * ANSClothes front-end behaviour.
 */
(function () {
	'use strict';

	// Price filter: this design hides WooCommerce's "Filter" submit button, but
	// this WC version doesn't auto-submit on slider release — so wire that up.
	if (typeof jQuery !== 'undefined') {
		jQuery(document.body).on('price_slider_change', function () {
			var amount = document.querySelector('.price_slider_amount');
			var form = amount ? amount.closest('form') : null;
			if (form) {
				form.submit();
			}
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		// Mobile navigation.
		var toggle = document.querySelector('.ans-nav-toggle');
		var nav = document.getElementById('ans-primary-nav');

		if (toggle && nav) {
			toggle.addEventListener('click', function () {
				var open = nav.classList.toggle('is-open');
				toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			});
		}

		// Header search.
		var searchToggle = document.querySelector('.ans-search-toggle');
		var searchForm = document.getElementById('ans-header-search');

		if (searchToggle && searchForm) {
			searchToggle.addEventListener('click', function () {
				var open = searchForm.classList.toggle('is-open');
				searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');

				if (open) {
					var field = searchForm.querySelector('input[type="search"]');

					if (field) {
						field.focus();
					}
				}
			});
		}

		// ── Archive: Collapsible filter groups ──────────────────────────────────
		var filterToggles = document.querySelectorAll('.ans-filter-group__toggle');

		filterToggles.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var expanded = btn.getAttribute('aria-expanded') === 'true';
				btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			});
		});

		// ── Archive: Layout toggle (3-col / 4-col) ──────────────────────────────
		var layoutBtns = document.querySelectorAll('.ans-layout-btn');
		var productsGrid = document.getElementById('ans-products-grid');

		layoutBtns.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var cols = btn.getAttribute('data-cols');
				if (!cols || !productsGrid) { return; }

				layoutBtns.forEach(function (b) { b.classList.remove('is-active'); });
				btn.classList.add('is-active');
				productsGrid.setAttribute('data-cols', cols);
			});
		});

		// ── Archive: Mobile sidebar toggle ──────────────────────────────────────
		var mobileFilterBtn = document.querySelector('.ans-filter-mobile-toggle');
		var filterSidebar = document.getElementById('ans-filter-sidebar');

		if (mobileFilterBtn && filterSidebar) {
			mobileFilterBtn.addEventListener('click', function () {
				var open = filterSidebar.classList.toggle('is-open');
				mobileFilterBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
				mobileFilterBtn.textContent = open ? 'Hide Filters' : 'Show Filters';
			});
		}

		// ── Single product: variation dropdowns → pill buttons ──────────────────
		// Keeps WooCommerce's own <select> in the DOM (hidden) so its native
		// variation-matching JS keeps working unmodified; pills just drive it.
		var variationSelects = document.querySelectorAll('.variations select');

		// WooCommerce only toggles the "Clear" link's visibility, which still
		// reserves layout space; flag the form so CSS can hide it outright
		// until something is actually selected.
		function syncSelectionState(form) {
			if (!form) { return; }
			var selects = form.querySelectorAll('.variations select');
			var hasSelection = Array.prototype.some.call(selects, function (s) {
				return !!s.value;
			});
			form.classList.toggle('ans-has-variation-selection', hasSelection);
		}

		variationSelects.forEach(function (select) {
			var wrapper = document.createElement('div');
			wrapper.className = 'ans-variation-pills';
			var form = select.closest('form.variations_form');

			function render() {
				syncSelectionState(form);
				wrapper.innerHTML = '';
				Array.prototype.forEach.call(select.options, function (opt) {
					if (!opt.value) { return; }

					var btn = document.createElement('button');
					btn.type = 'button';
					btn.className = 'ans-variation-pill';
					btn.textContent = opt.textContent;

					if (opt.disabled) {
						btn.classList.add('is-disabled');
					}
					if (select.value === opt.value) {
						btn.classList.add('is-active');
					}

					btn.addEventListener('click', function () {
						if (opt.disabled) { return; }
						select.value = opt.value;
						select.dispatchEvent(new Event('change', { bubbles: true }));
						render();
					});

					wrapper.appendChild(btn);
				});
			}

			render();
			select.addEventListener('change', render);
			select.classList.add('ans-visually-hidden');
			select.insertAdjacentElement('afterend', wrapper);
		});

		// ── Single product: size chart guide ─────────────────────────────────────
		// The trigger is rendered before the variations form as a fallback
		// position (for products with no size attribute); when a size selector
		// label row exists, move the trigger into it so it reads as
		// "Select Size — Size Chart Guide" on one line.
		var sizeChartTrigger = document.getElementById('ans-size-chart-trigger');
		var sizeChartModal = document.getElementById('ans-size-chart-modal');

		if (sizeChartTrigger && sizeChartModal) {
			var sizeLabel = document.querySelector('.variations th.label');

			if (sizeLabel) {
				sizeLabel.classList.add('ans-has-size-chart');
				sizeLabel.appendChild(sizeChartTrigger);
			}

			var sizeChartLastFocused = null;

			function openSizeChart() {
				sizeChartLastFocused = document.activeElement;
				sizeChartModal.classList.add('is-open');
				sizeChartModal.setAttribute('aria-hidden', 'false');
				var close = sizeChartModal.querySelector('.ans-size-chart-modal__close');
				if (close) { close.focus(); }
			}

			function closeSizeChart() {
				sizeChartModal.classList.remove('is-open');
				sizeChartModal.setAttribute('aria-hidden', 'true');
				if (sizeChartLastFocused && sizeChartLastFocused.focus) { sizeChartLastFocused.focus(); }
			}

			sizeChartTrigger.addEventListener('click', openSizeChart);

			sizeChartModal.addEventListener('click', function (event) {
				if (event.target.closest('[data-size-chart-close]')) {
					closeSizeChart();
				}
			});

			document.addEventListener('keydown', function (event) {
				if (event.key === 'Escape' && sizeChartModal.classList.contains('is-open')) {
					closeSizeChart();
				}
			});
		}

		// ── Single product: −/+ quantity stepper ────────────────────────────────
		document.querySelectorAll('.single-product .quantity').forEach(function (qty) {
			var input = qty.querySelector('input.qty');

			if (!input || qty.querySelector('.ans-qty-btn')) { return; }

			function stepBy(direction) {
				var stepSize = parseFloat(input.getAttribute('step')) || 1;
				var min = parseFloat(input.getAttribute('min'));
				var max = parseFloat(input.getAttribute('max'));
				var current = parseFloat(input.value);

				if (isNaN(current)) { current = isNaN(min) ? 1 : min; }

				var next = current + (direction * stepSize);

				if (!isNaN(min) && next < min) { next = min; }
				if (!isNaN(max) && next > max) { next = max; }

				input.value = next;
				input.dispatchEvent(new Event('change', { bubbles: true }));
			}

			function makeButton(label, direction) {
				var btn = document.createElement('button');
				btn.type = 'button';
				btn.className = 'ans-qty-btn';
				btn.setAttribute('aria-label', label);
				btn.textContent = direction < 0 ? '−' : '+';
				btn.addEventListener('click', function () { stepBy(direction); });
				return btn;
			}

			input.insertAdjacentElement('beforebegin', makeButton('Decrease quantity', -1));
			input.insertAdjacentElement('afterend', makeButton('Increase quantity', 1));
		});

		// ── Cart drawer ─────────────────────────────────────────────────────────
		var cartDrawer = document.getElementById('ans-cart-drawer');

		if (cartDrawer && typeof ansCart !== 'undefined') {
			var cartBody = cartDrawer.querySelector('.ans-cart-drawer__body');
			var lastFocused = null;

			function openCart() {
				lastFocused = document.activeElement;
				cartDrawer.classList.add('is-open');
				cartDrawer.setAttribute('aria-hidden', 'false');
				document.body.classList.add('ans-cart-open');
				var close = cartDrawer.querySelector('.ans-cart-drawer__close');
				if (close) { close.focus(); }
			}

			function closeCart() {
				cartDrawer.classList.remove('is-open');
				cartDrawer.setAttribute('aria-hidden', 'true');
				document.body.classList.remove('ans-cart-open');
				if (lastFocused && lastFocused.focus) { lastFocused.focus(); }
			}

			// The header link keeps its href so it still works without JS.
			var cartButtons = document.querySelectorAll('.ans-cart-btn');
			cartButtons.forEach(function (btn) {
				btn.addEventListener('click', function (event) {
					event.preventDefault();
					openCart();
				});
			});

			cartDrawer.addEventListener('click', function (event) {
				if (event.target.closest('[data-cart-close]')) {
					closeCart();
				}
			});

			document.addEventListener('keydown', function (event) {
				if (event.key === 'Escape' && cartDrawer.classList.contains('is-open')) {
					closeCart();
				}
			});

			function setBusy(busy) {
				cartDrawer.classList.toggle('is-busy', !!busy);
			}

			function updateLine(key, quantity) {
				setBusy(true);

				var body = new URLSearchParams();
				body.append('action', 'ansclothes_cart_update');
				body.append('nonce', ansCart.nonce);
				body.append('key', key);
				body.append('quantity', quantity);

				fetch(ansCart.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: body.toString()
				})
					.then(function (res) { return res.json(); })
					.then(function (payload) {
						if (!payload || !payload.success) { return; }

						cartBody.innerHTML = payload.data.contents;

						document.querySelectorAll('.ans-cart-count, .ans-cart-drawer__badge')
							.forEach(function (el) { el.textContent = payload.data.count; });

						// Let WooCommerce refresh anything else bound to the cart.
						if (typeof jQuery !== 'undefined') {
							jQuery(document.body).trigger('wc_fragment_refresh');
						}
					})
					.catch(function () { /* leave the drawer as-is on failure */ })
					.then(function () { setBusy(false); });
			}

			// Delegated so the handlers survive the drawer being re-rendered.
			cartDrawer.addEventListener('click', function (event) {
				var line = event.target.closest('.ans-cart-line');
				if (!line) { return; }

				var key = line.getAttribute('data-key');
				var input = line.querySelector('.ans-cart-qty__input');

				if (event.target.closest('.ans-cart-line__remove')) {
					updateLine(key, 0);
					return;
				}

				var stepBtn = event.target.closest('.ans-cart-qty__btn');
				if (stepBtn && input) {
					var next = (parseInt(input.value, 10) || 0) + parseInt(stepBtn.getAttribute('data-step'), 10);
					if (next < 0) { next = 0; }
					input.value = next;
					updateLine(key, next);
				}
			});

			cartDrawer.addEventListener('change', function (event) {
				if (!event.target.classList.contains('ans-cart-qty__input')) { return; }
				var line = event.target.closest('.ans-cart-line');
				if (!line) { return; }
				var value = parseInt(event.target.value, 10);
				updateLine(line.getAttribute('data-key'), isNaN(value) || value < 0 ? 0 : value);
			});

			// Open the drawer right after an AJAX add-to-cart.
			if (typeof jQuery !== 'undefined') {
				jQuery(document.body).on('added_to_cart', function () { openCart(); });
			}
		}

		// ── Checkout order review: remove a line ────────────────────────────────
		// WooCommerce's own update_checkout AJAX replaces the whole
		// .woocommerce-checkout-review-order-table node on every refresh, so the
		// click handler is delegated from #order_review (which it only refills,
		// never replaces) rather than bound to the table itself.
		var orderReview = document.getElementById('order_review');

		if (orderReview && typeof ansCart !== 'undefined') {
			orderReview.addEventListener('click', function (event) {
				var button = event.target.closest('.ans-co-line__remove');
				if (!button) { return; }

				var row = button.closest('tr[data-key]');
				if (!row) { return; }

				var body = new URLSearchParams();
				body.append('action', 'ansclothes_cart_update');
				body.append('nonce', ansCart.nonce);
				body.append('key', row.getAttribute('data-key'));
				body.append('quantity', 0);

				button.disabled = true;

				fetch(ansCart.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: body.toString()
				})
					.then(function (res) { return res.json(); })
					.then(function (payload) {
						if (!payload || !payload.success) {
							button.disabled = false;
							return;
						}

						if (payload.data.count === 0 && ansCart.cartUrl) {
							window.location.href = ansCart.cartUrl;
							return;
						}

						// Cart is already updated server-side; ask WooCommerce's own
						// checkout script to redraw #order_review and totals from it.
						if (typeof jQuery !== 'undefined') {
							jQuery(document.body).trigger('update_checkout');
							jQuery(document.body).trigger('wc_fragment_refresh');
						}
					})
					.catch(function () { button.disabled = false; });
			});
		}

		// ── Cash-on-delivery checkout modal ─────────────────────────────────────
		var codModal = document.getElementById('ans-cod-modal');

		if (codModal && typeof ansCart !== 'undefined') {
			var codForm = codModal.querySelector('.ans-cod__form');
			var codCart = codModal.querySelector('.ans-cod__cart');
			var codSubmit = codModal.querySelector('.ans-cod__submit');
			var codError = codModal.querySelector('.ans-cod__error');

			function codShipping() {
				var picked = codModal.querySelector('input[name="shipping"]:checked');
				return picked ? picked.value : '';
			}

			function openCod() {
				codModal.classList.add('is-open');
				codModal.setAttribute('aria-hidden', 'false');
				document.body.classList.add('ans-cart-open');
				var first = codModal.querySelector('#ans-cod-name');
				if (first) { first.focus(); }
			}

			function closeCod() {
				codModal.classList.remove('is-open');
				codModal.setAttribute('aria-hidden', 'true');
				document.body.classList.remove('ans-cart-open');
			}

			function showCodError(message) {
				codError.textContent = message || '';
				codError.classList.toggle('is-visible', !!message);
			}

			// Rebuilds the lines, totals and button label together so they can
			// never disagree with each other.
			function refreshCod(extra) {
				var body = new URLSearchParams();
				body.append('action', 'ansclothes_cod_refresh');
				body.append('nonce', ansCart.nonce);
				body.append('shipping', codShipping());

				if (extra) {
					body.append('key', extra.key);
					body.append('quantity', extra.quantity);
				}

				codModal.classList.add('is-busy');

				return fetch(ansCart.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: body.toString()
				})
					.then(function (res) { return res.json(); })
					.then(function (payload) {
						if (!payload || !payload.success) { return; }

						codCart.innerHTML = payload.data.summary;
						codSubmit.textContent = payload.data.buttonLabel;

						document.querySelectorAll('.ans-cart-count, .ans-cart-drawer__badge')
							.forEach(function (el) { el.textContent = payload.data.count; });

						if (payload.data.isEmpty) { closeCod(); }

						if (typeof jQuery !== 'undefined') {
							jQuery(document.body).trigger('wc_fragment_refresh');
						}
					})
					.catch(function () { /* keep the current view on failure */ })
					.then(function () { codModal.classList.remove('is-busy'); });
			}

			// Open from the cart drawer's Order Now, in place of the checkout page.
			document.querySelectorAll('.ans-cart-drawer__checkout').forEach(function (btn) {
				btn.addEventListener('click', function (event) {
					event.preventDefault();
					var drawer = document.getElementById('ans-cart-drawer');
					if (drawer) {
						drawer.classList.remove('is-open');
						drawer.setAttribute('aria-hidden', 'true');
					}
					openCod();
					refreshCod();
				});
			});

			// Buy Now posts the item to the cart and returns with this flag set.
			if (/[?&]ans_cod=1/.test(window.location.search)) {
				openCod();
				if (window.history.replaceState) {
					var cleaned = window.location.href
						.replace(/([?&])ans_cod=1&?/, '$1')
						.replace(/[?&]$/, '');
					window.history.replaceState({}, '', cleaned);
				}
			}

			codModal.addEventListener('click', function (event) {
				if (event.target.closest('[data-cod-close]')) {
					closeCod();
					return;
				}

				var step = event.target.closest('.ans-cod-qty__btn');
				if (step) {
					var item = step.closest('.ans-cod-item');
					var valueEl = item.querySelector('.ans-cod-qty__value');
					var next = (parseInt(valueEl.textContent, 10) || 0) + parseInt(step.getAttribute('data-step'), 10);
					if (next < 0) { next = 0; }
					refreshCod({ key: item.getAttribute('data-key'), quantity: next });
				}
			});

			codModal.addEventListener('change', function (event) {
				if (event.target.name !== 'shipping') { return; }

				codModal.querySelectorAll('.ans-cod-ship').forEach(function (label) {
					label.classList.toggle('is-selected', label.contains(event.target));
				});

				refreshCod();
			});

			document.addEventListener('keydown', function (event) {
				if (event.key === 'Escape' && codModal.classList.contains('is-open')) {
					closeCod();
				}
			});

			codForm.addEventListener('submit', function (event) {
				event.preventDefault();
				showCodError('');

				var body = new URLSearchParams();
				body.append('action', 'ansclothes_cod_place_order');
				body.append('nonce', ansCart.nonce);
				body.append('name', codForm.querySelector('[name="name"]').value);
				body.append('phone', codForm.querySelector('[name="phone"]').value);
				body.append('address', codForm.querySelector('[name="address"]').value);
				body.append('note', codForm.querySelector('[name="note"]').value);
				body.append('shipping', codShipping());

				codSubmit.disabled = true;
				codModal.classList.add('is-busy');

				fetch(ansCart.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: body.toString()
				})
					.then(function (res) { return res.json(); })
					.then(function (payload) {
						if (payload && payload.success) {
							window.location.href = payload.data.redirect;
							return;
						}

						showCodError((payload && payload.data && payload.data.message) || 'Something went wrong. Please try again.');
						codSubmit.disabled = false;
						codModal.classList.remove('is-busy');
					})
					.catch(function () {
						showCodError('Something went wrong. Please try again.');
						codSubmit.disabled = false;
						codModal.classList.remove('is-busy');
					});
			});
		}

		// Reveal sections on scroll.
		var revealables = document.querySelectorAll('.ans-reveal');
		var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		// Hero slider.
		var slides = document.querySelectorAll('.ans-hero__slide');
		var dots = document.querySelectorAll('.ans-hero__dots span');

		if (!reduced && slides.length > 1) {
			var activeSlide = 0;

			window.setInterval(function () {
				slides[activeSlide].classList.remove('is-active');

				if (dots[activeSlide]) {
					dots[activeSlide].classList.remove('is-active');
				}

				activeSlide = (activeSlide + 1) % slides.length;
				slides[activeSlide].classList.add('is-active');

				if (dots[activeSlide]) {
					dots[activeSlide].classList.add('is-active');
				}
			}, 4500);
		}

		if (!revealables.length) {
			return;
		}

		if (reduced || !('IntersectionObserver' in window)) {
			revealables.forEach(function (el) {
				el.classList.add('is-visible');
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{ rootMargin: '0px 0px -12% 0px', threshold: 0.05 }
		);

		revealables.forEach(function (el) {
			observer.observe(el);
		});
	});
})();

