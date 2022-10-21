export default {
    post: async function (endpoint, body = {}) {
        body._token = endpoint.csrfToken;

        const response = await fetch(endpoint.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(body)
        });

        return response.json();
    }
}
