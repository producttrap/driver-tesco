# Sample Product

```
ProductTrap\DTOs\Product {
  +identifier: "301511619"
  +status: ProductTrap\Enums\Status {
    +name: "Available"
    +value: "available"
  }
  +sku: "301511619"
  +gtin: "00000050171248"
  +name: "John West No Drain Tuna Steak In Oil Fridge Pot 110G"
  +description: "Tuna Steak with a little Sunflower Oil.; 100% Traceable; Track Your Can john-west.co.uk"
  +url: "https://tesco.com/groceries/en-GB/products/301511619"
  +ingredients: "Tuna (93%); Sunflower Oil; Salt"
  +price: ProductTrap\DTOs\Price {
    +amount: 2.0
    +wasAmount: null
    +saleName: null
    +currency: ProductTrap\Enums\Currency {
      +name: "GBP"
      +value: "GBP"
    }
  }
  +unitAmount: ProductTrap\DTOs\UnitAmount {
    +unit: ProductTrap\Enums\Unit {
      +name: "GRAM"
      +value: "g"
    }
    +amount: 100.0
  }
  +unitPrice: ProductTrap\DTOs\UnitPrice {
    +unitAmount: ProductTrap\DTOs\UnitAmount {
      +unit: ProductTrap\Enums\Unit {
        +name: "KILOGRAM"
        +value: "kg"
      }
      +amount: 1.0
    }
    +price: ProductTrap\DTOs\Price {
      +amount: 20.0
      +wasAmount: null
      +saleName: null
      +currency: null
    }
  }
  +brand: ProductTrap\DTOs\Brand {
    +identifier: "JOHN WEST"
    +name: "JOHN WEST"
    +url: null
  }
  +images: array:1 [
    0 => "https://digitalcontent.api.tesco.com/v2/media/ghs/80791ac3-a2ab-4db9-88da-01631a6eaef9/d7fcce24-d9fc-4747-923c-9bddb8b3b94a_1749225173.jpeg?h=225&amp;w=225"
  ]
  +categories: []
  +raw: array:1 [
    "html" => "/* HTML OF PAGE HERE */"
  ]
}

```