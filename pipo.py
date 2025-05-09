import g4f
import requests
from g4f.client import Client
from urllib.parse import unquote
import asyncio

for attr in dir(g4f.Model):
    if not attr.startswith("__"):
        print(attr, getattr(g4f.Model, attr))

ranks = [
    "diamond"
]
client = Client()
for rank in ranks:
    prompt = f"Do a logo that represents a rank '{rank}' of a MOBA game, in style of the logo of the game with the name of the rank"
    image_response = client.images.generate(
        model="dall-e-3",
        prompt=prompt,
        # provider=g4f.Provider.PollinationsImage,  # opzionale
    )
    # Prendi il primo oggetto Image dalla lista data
    image_url = image_response.data[0].url
    # L'URL vero è dopo '?url='
    if "?url=" in image_url:
        image_url = image_url.split("?url=")[1]
    # Decodifica l'URL
    image_url = unquote(image_url)
    # Scarica l'immagine
    img_data = requests.get(image_url).content
    with open(f"{rank}.png", "wb") as f:
        f.write(img_data)
    print(f"Salvato {rank}.png")

try:
    loop = asyncio.get_event_loop()
    loop.close()
except:
    pass