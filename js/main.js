// A $( document ).ready() block.
$(document).ready(function() {
    console.log("ready!");

    // Haal de wisselkoers op en laad dan alle coins
    fetchEuroToUsdRate(function(euroToUsdRate) {
        fetchAllCoins(euroToUsdRate);
    });

    addButtonFunction();
    fetchCoinHistory7Days();
    getExchanges();
    getCryptoNews();
});

var chartInstance = null;

// Get the list of all coins
function fetchAllCoins(euroToUsdRate) {

	var settings = {
        "url": "https://api.coincap.io/v2/assets",
        "method": "GET",
        "timeout": 0,
    };

    $.ajax(settings).done(function (response) {
        
        coins = response;

		//console.log(coins.data);

        // Leeg de tabel eerst, zodat er geen dubbele data komt
        $("#characters-table tbody").empty();

        for (let i = 0; i < coins.data.length; i++) {

            var coinLogo = `https://assets.coincap.io/assets/icons/${coins.data[i].symbol.toLowerCase()}@2x.png`;
        
            let symbol = coins.data[i].symbol;
            let name = coins.data[i].name;
            let time = coins.data[i].volumeUsd24Hr;
            
            // Gebruik toFixed() om het aantal decimalen te beperken
            let priceUSD = parseFloat(coins.data[i].priceUsd).toFixed(2);
			let priceEUR = parseFloat(coins.data[i].priceUsd) * euroToUsdRate;
            let marketCap = parseFloat(coins.data[i].marketCapUsd);
            let hr = parseFloat(coins.data[i].vwap24Hr).toFixed(2);
			let newMarketCap = marketCap.toFixed(2);
			let newPriceEUR = priceEUR.toFixed(2);
			
			//more info button
            let moreinfo = `
            <button type='button' 
                class='btn btn-primary character-info-btn' 
                data-bs-toggle="modal" 
                data-bs-target="#chartModal" 
                data-id='${symbol}' 
                data-name='${name}' 
                data-price='$${priceUSD}'
				data-eurprice = '${newPriceEUR}' 
                data-marketCap='${newMarketCap}' 
                data-hr='${hr}'
                >More info
            </button>`

			//add coins to cryptowallet button
            let cryptofolio = `
				<button id='addbutton' 
					type='button'
					class='btn btn-primary'
					data-bs-toggle="modal" 
					data-bs-target="#coinModal"
					data-name='${name}'
					data-price='$${priceUSD}'
					>Add
				</button>
            `;
            const row = `
				<tr>
					<td><img style='height: 30px' src='${coinLogo}'>${symbol}</td>
					<td>${name}</td>
					<td>$${priceUSD}</td>
					<td>€${newPriceEUR}</td> 
					<td>${newMarketCap}</td>
					<td>${hr}</td>
					<td>${moreinfo}</td>
					<td class='hiddenRow' id='hiddenRow' >${cryptofolio}</td>
				</tr>
			`;
            
            // Voeg de rij toe aan de tbody van de tabel
            $("#characters-table tbody").append(row);
            
            toggleRowVisibility();
        }

		// Event listener toevoegen aan alle 'More info' knoppen
		$('.character-info-btn').on('click', function() {
			// Haal de marketCap data op van de geklikte knop
			const marketCap = $(this).data('marketcap');
			// Formatteer zonder scheidingstekens voor duizendtallen
			document.getElementById('marketcap').textContent = `$${parseFloat(marketCap).toLocaleString('nl-NL', {useGrouping: false})}`;
		});

    });
}

//get all exchanges
const getExchanges = () => {
	var settings = {
        "url": "https://api.coincap.io/v2/exchanges",
        "method": "GET",
        "timeout": 0,
    };

	$.ajax(settings).done(function (response) {

		exchanges = response;

		// console.log(exchanges.data);

		for (let i = 0; i < exchanges.data.length; i++) {

			let name = exchanges.data[i].name;
			let rank = exchanges.data[i].rank;
			let volume24hr = parseFloat(exchanges.data[i].volumeUsd).toFixed(2);
			let websiteurl = exchanges.data[i].exchangeUrl;

			const exchangerow = `
				<div class="exchange-row row border border-light p-3 mb-3 align-items-center login_backcolor">
					<div class="col-md-4 text-center">
						<span class="fw-bold">Name of the exchange:</span> ${name}
					</div>
					<div class="col-md-2 text-center">
						<span class="fw-bold">Rank:</span> ${rank}
					</div>
					<div class="col-md-3 text-center">
						<span class="fw-bold">Volume (24Hr):</span> $${volume24hr}
					</div>
					<div class="col-md-3 text-center">
						<a class="text-decoration-none" href="${websiteurl}" target="_blank">Visit Website</a>
					</div>
				</div>
			`;
			
			//voegt de data toe aan de div
			$("#exchange").append(exchangerow);
		}
	});
}

//get all cryptonews
const getCryptoNews = () => {

	var settings = {
        "url": "https://newsdata.io/api/1/news?apikey=pub_5793129f99cd402f9763250d07a7ea365115b&q=crypto%20news",
        "method": "GET",
        "timeout": 0,
    };
	
	$.ajax(settings).done(function (response) {

		getcryptonews = response;

		// console.log(getcryptonews.results);

		for (let i = 0; i < getcryptonews.results.length; i++) {

			let title = getcryptonews.results[i].title;
			let image = getcryptonews.results[i].image_url;
			let link = getcryptonews.results[i].link;
			let description = getcryptonews.results[i].description;


			const cryptonews = `
				<div>
					<div>
						<h1>${title}</h1>
					</div>
					<div>
						<a href='${link}'><img class='w-25 h-25' src='${image}' alt='crypto news image'></a>
					</div>
					<div>
						<p>${description}</p>
					</div>
					<hr>

				</div>
			`;
			
			//voegt de data toe aan de div
			$("#cryptonews").append(cryptonews);
		}
	});
}

// Get the Euro to USD conversion rate
function fetchEuroToUsdRate(callback) {
    $.ajax({
        url: `https://api.coincap.io/v2/rates/euro`,
        method: "GET",
        success: function (response) {
            const rateUsd = parseFloat(response.data.rateUsd);
            const euroToUsdRate = 1 / rateUsd; 
            callback(euroToUsdRate);
        },
        error: function (error) {
            console.error("Error fetching Euro rate:", error);
            callback(1); // standaard 1
        }
    });
}

const addButtonFunction = () => {
	
	$('#coinModal').on('shown.bs.modal', function (event) {
		// Haal de knop op die de modal opende
		let button = $(event.relatedTarget);
	
		// Haal de data uit de knop (name en price)
		let coinName = button.data('name');
		let coinPrice = button.data('price');

		//console.log(amount);
	
		// Vul de modal met deze waarden
		let modal = $(this);
		modal.find('.modal-title').text(coinName + ' Details');  // Titel updaten met de coin name
		modal.find('#coinprice').text(coinPrice);  // Prijs invullen

		modal.find('#addbutton').off('click').on('click', function() {
            let amount = $('#number').val();
            saveCoinDetails(coinName, coinPrice, amount, event);
        });
	});
	
}

const saveCoinDetails = (coinName, coinPrice, amount, event) => {

	event.preventDefault();

	// console.log(coinName);

	$('#coinModal').on('show.bs.modal', function () {
		let coinmessage = document.getElementById("coinmessage");
		coinmessage.innerText = ""; 
	});

	$.ajax({
		type: 'POST',
		url: 'savecoindetails.php',
		data: {
			name: coinName,
			price: coinPrice,
			amount: amount
		},
		success: function(response) {

			let coinmessage = document.getElementById("coinmessage");
			coinmessage.innerText = "Coin successfully added to the wallet";
		},
		error: function(xhr, status, error) {
			console.error("Fout bij het versturen: " + error);
		}
	});
};

const toggleRowVisibility = () => {

	let hiddenRowsArray = [...document.querySelectorAll('.hiddenRow')]; //make it an array

	hiddenRowsArray.forEach(function(row) {

		if(typeof isLoggedIn !== 'undefined') {

			if (isLoggedIn) {
				// Verwijder de 'hidden' class om de rijen te tonen
				row.classList.remove('hiddenRow');
			} 
			else {
				// Voeg de 'hidden' class toe om de rijen te verbergen
				row.classList.add('hiddenRow');
			}	
		}
    });
}

const walletSave = (event) => {

	//console.log("TEST");

	let row = event.target.closest('tr');
	var walletAmount = $(row).find('.walletamount').val();
	let id = row.querySelector('td').innerText.trim();

	// let walletAmount = $("#walletamount").val();
	//console.log(walletAmount);

	$.ajax({
		type: "POST",
		url: 'updateamount.php',
		data: {
			idAmount: id,
			amount: walletAmount,
		},
		success: function (data) {
			//console.log(data);
			// console.log("amount is geupdate");

			let updateamount = document.getElementById("coinmessage");
			updateamount.innerText = "Amount successfully edited"
		},
		error: function (error) {
			//console.log(`Error ${error}`);
		}
	})
	
}

const walletDelete = (event) => {

	const row = event.target.closest('tr');
	const id = row.querySelector('td').innerText;

	//console.log(id);

	$.ajax({
		type: "POST",
		url: 'deleterow.php',
		data: {
			walletId: id,
		},
		success: function (data) {
			console.log(data);

			let deleterow = document.getElementById("deletemessage");
			deleterow.innerText = "Row successfully deleted"
			$(this).closest('tr').remove();
		},
		error: function (error) {
			console.log(`Error ${error}`);
		}
	});
}

// Get historical data of the coin for the past 24 hours
function fetchCoinHistory7Days() {
	// console.log(name);
	$('#chartModal').on('shown.bs.modal', function (event) {

		let button = $(event.relatedTarget); 
		let name = button.data('name').toLowerCase();
		console.log(name);
		document.getElementById('currentPrice').textContent = button.data('price');
		document.getElementById('currentName').textContent = name;

		$.ajax({
			url: `https://api.coincap.io/v2/assets/${name}/history?interval=d1&limit=7`,
			method: "GET",
			success: function (response) {
				var pricedatedata = response.data;
				
	
				if (!pricedatedata || pricedatedata.length < 7) {
					console.error("Geen geldige historische data ontvangen");
					return;
				}
	
				var labels = [];
				var prices = [];
	
				for (let i = pricedatedata.length - 7; i < pricedatedata.length; i++) {
					let coinprice = parseFloat(pricedatedata[i].priceUsd).toFixed(2); // Prijs ophalen en formatteren
					let date = new Date(pricedatedata[i].time).toLocaleDateString(); // Datum formatteren
			
					prices.push(coinprice); // Voeg de prijs toe aan de prices array
					labels.push(date); // Voeg de datum toe aan de labels array
				}
	
				// console.log(pricedatedata[0].priceUsd);
				// console.log(prices);
				// console.log(labels);
				
				generateChart(labels, prices);
			}
		});
	});

    
}

//generates the chart with the historical information from the past year
function generateChart(chartDate, chartPrice) {
    // Verwijder de eventuele bestaande grafiek
    var ctx = document.getElementById('myChart').getContext('2d');
    var graph = Chart.getChart("myChart");

    if (graph) {
        graph.destroy();
    }

    // Maak de nieuwe grafiek aan
    graph = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartDate,
            datasets: [{
                label: "Price in USD",
                borderColor: '#3e95cd',
                data: chartPrice,
                fill: false,
            }]
        },
        options: {
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Price'
                    }
                }
            },
            elements: {
                point: {
                    radius: 0
                }
            }
        }
    });
}


