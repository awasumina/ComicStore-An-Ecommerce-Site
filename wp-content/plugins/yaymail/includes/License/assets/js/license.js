jQuery(document).ready(function () {
	const slug = "yaymail";
	const REST_URL =
	  window[`${slug}LicenseData`].apiSettings.restUrl;
	const REST_NONCE = window[`${slug}LicenseData`].apiSettings.restNonce;
	const POST_OPTIONS = {
	  method: "POST",
	  headers: {
		"Content-type": "application/json",
		"x-wp-nonce": REST_NONCE
	  },
	};
  
	jQuery(".yaycommerce-license-layout").on(
	  "click",
	  `.yaycommerce-activate-license-button[data-plugin*='${slug}']`,
	  handleActivate
	);
	jQuery(".yaycommerce-license-layout").on(
	  "click",
	  `.yaycommerce-update-license[data-plugin*='${slug}']`,
	  handleUpdate
	);
	jQuery(".yaycommerce-license-layout").on(
	  "click",
	  `.yaycommerce-remove-license[data-plugin*='${slug}']`,
	  handleRemove
	);
	jQuery(".yaycommerce-license-layout").on(
	  "click",
	  `#${slug}_license_card .yaycommerce-license-message .yaycommerce-license-message__close`,
	  function( ) {hideMessage(slug) }
	);
  
	async function handleActivate(event) {
	  event.preventDefault();
	  clearMessages();
	  const { plugin } = jQuery(this).data();
	  beforeCallAPI(plugin, "activate");
	  hideMessage(plugin);
	  const licenseKey = jQuery(`#${plugin}_license_input`).val();
  
	  const response = await fetch(`${REST_URL}/license/activate`, {
		...POST_OPTIONS,
		body: JSON.stringify({
		  license_key: licenseKey,
		  plugin,
		}),
	  }).then((response) => response.json());
	  afterCallAPI(plugin, "activate");
	  if (response.success) {
		replaceSuccessfullContent(response);
	  }
	  showMessage(response, "activate");
	}
  
	async function handleUpdate(event) {
	  event.preventDefault();
	  clearMessages();
	  const { plugin } = jQuery(this).data();
	  beforeCallAPI(plugin, "update");
	  const response = await fetch(`${REST_URL}/license/update`, {
		...POST_OPTIONS,
		body: JSON.stringify({
		  plugin,
		}),
	  }).then((response) => response.json());
	  afterCallAPI(plugin, "update");
	  if (response.success) {
		replaceSuccessfullContent(response);
	  } else {
		replaceActivatorContent(response);
	  }
	  showMessage(response, "update");
	}
	async function handleRemove(event) {
	  event.preventDefault();
	  clearMessages();
	  const { plugin } = jQuery(this).data();
	  beforeCallAPI(plugin, "remove");
	  const response = await fetch(`${REST_URL}/license/delete`, {
		...POST_OPTIONS,
		body: JSON.stringify({
		  plugin,
		}),
	  }).then((response) => response.json());
	  afterCallAPI(plugin, "remove");
	  replaceActivatorContent(response);
	  showMessage({success: true}, "remove")
	}
  
	function replaceSuccessfullContent(data) {
	  jQuery(`#${data.slug}_license_card`).replaceWith( data.html );
	}
	function replaceActivatorContent(data) {
	  jQuery(`#${data.slug}_license_card`).replaceWith( data.html );
	}
  
	function hideMessage(slug) {
	  jQuery(`#${slug}_license_card .yaycommerce-license-message`).removeClass(
		"show"
	  );
	  jQuery(`#${slug}_license_card .yaycommerce-license-message`).html("");
	}
	function clearMessages() {
	  jQuery(".message").removeClass("active");
	}
	function showMessage(data, action) {
	  const { slug, success, message } = data;
	  if ( success ) {
		const id = `message-${action}-success`;
		document.getElementById(id).classList.add("active");
		setTimeout(() => {
		  document.getElementById(id).classList.remove("active");
		}, 2000);
	  } else {
		const id = `message-${action}-error`;
		document.getElementById(id).classList.add("active");
		setTimeout(() => {
		  document.getElementById(id).classList.remove("active");
		}, 2000);
	  }
	}
  
  
	function beforeCallAPI(plugin, action) {
	  if (action === "activate") {
		jQuery(`.yaycommerce-activate-license-button[data-plugin=${plugin}]`)
		  .find(".activate-loading")
		  .css("display", "inline-flex");
	  }
  
	  if (action === "update") {
		jQuery(`.yaycommerce-update-license[data-plugin=${plugin}]`)
		  .find(".activate-loading")
		  .css("display", "inline-flex");
	  }
  
	  if (action === "remove") {
		jQuery(`.yaycommerce-remove-license[data-plugin=${plugin}]`)
		  .find(".activate-loading")
		  .css("display", "inline-flex");
	  }
  
	  jQuery(`.yaycommerce-activate-license-button[data-plugin=${plugin}]`).attr(
		"disabled",
		true
	  );
	  jQuery(`.yaycommerce-update-license[data-plugin=${plugin}]`).attr(
		"disabled",
		true
	  );
	  jQuery(`.yaycommerce-remove-license[data-plugin="${plugin}"]`).attr(
		"disabled",
		true
	  );
	}
  
	function afterCallAPI(plugin, action) {
	  if (action === "activate") {
		jQuery(`.yaycommerce-activate-license-button[data-plugin=${plugin}]`)
		  .find(".activate-loading")
		  .hide();
	  }
  
	  if (action === "update") {
		jQuery(`.yaycommerce-update-license[data-plugin=${plugin}]`)
		  .find(".activate-loading")
		  .hide();
	  }
  
	  if (action === "remove") {
		jQuery(`.yaycommerce-remove-license[data-plugin=${plugin}]`)
		  .find(".activate-loading")
		  .hide();
	  }
	  jQuery(`.yaycommerce-activate-license-button[data-plugin=${plugin}]`).attr(
		"disabled",
		false
	  );
	  jQuery(`.yaycommerce-update-license[data-plugin=${plugin}]`).attr(
		"disabled",
		false
	  );
	  jQuery(`.yaycommerce-remove-license[data-plugin="${plugin}"]`).attr(
		"disabled",
		false
	  );
	}
  });