import requests

def fetch_page(url):
    """Fetch the content of a web page."""
    response = requests.get(url)
    response.raise_for_status()  # Raise an error for bad responses
    return response.text

if __name__ == "__main__":
    url = "https://cellphones.com.vn/mobile/apple.html"
    content = fetch_page(url)
    print(content)