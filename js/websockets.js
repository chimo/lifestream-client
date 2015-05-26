( function() {
    /*global console: false, JSON: false*/
    "use strict";

    if ( "WebSocket" in window ) {
        var ws = new WebSocket( window.location.origin.replace( /https?/, "ws" ) + ":" + window.ls.port ),
            formatDate;

        formatDate = function( date ) {
            var d = new Date( date );

            // Fallback to "now" if date is invalid
            if ( isNaN( d.getTime() ) ) {
                d = new Date();
            }

            return d.toISOString().slice( 0, -5 ).replace( "T", " " );
        };

        ws.onopen = function( /*event*/ ) {
          console.log( "connection established" );
        };

        ws.onerror = function( /*event*/ ) {
            console.log( "An error occured" );
        };

        ws.onclose = function( event ) {
            var closedDetails = ( event.wasClean ) ? " cleanly" : " unexpectedly",
                reason = event.reason;

            console.log( "WebSocket connection closed" + closedDetails );
            console.log( "Code: " + event.code + " ( see: https://developer.mozilla.org/en-US/docs/Web/API/CloseEvent )" );

            // Don't print reason if it's an empty string
            if ( reason === "" ) {
                console.log( "Reason: " + event.reason );
            }
        };

        ws.onmessage = function( event ) {
            var item = document.createElement( "li" ),
                list = document.getElementById( "list" );

            event = JSON.parse( event.data );

            item.setAttribute( "class", event.type );
            item.innerHTML = "<figure>" +
                               "<figcaption>" + event.title + "</figcaption>" +
                               "<blockquote cite='" + event.source + "'>" + event.content + "</blockquote>" +
                               "<footer><a href='" + event.source + "'>" + formatDate( event.published ) + "</a></footer>" +
                           "</figure>";
            list.insertBefore( item, list.firstChild );
        };
      }
}() );
