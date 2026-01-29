fix the problem with the pricing structure.

for context:
for our prices db structure. we have
  pricing_levels and we have prices table. we also
  have product_types like sc, medium, large and so
  on. we also have products table that contains the
  flavor/variant.

tables:
- pricing_levels - @pricelevels_tb.png
- prices - @prices_tb.png
- product_types - @product_types_tb.png structure
- products - @products_tb.png structure - stored here is the product types + product variants
- product variants - @product_variants_tb.png structure

wrong saving of the price is: ( products -> prices )
 - SAMPLE
    - SC_RR -> PRICE
    - MC_SB -> PRICE
    - SO ON...

correct: (product types -> prices)
 - SC -> PRICE
 - MC -> PRICE
 - SO ON...

we need to fix the saving of the price to the correct structure.

also, we need to fix the fetching of the price to the correct structure.


for sample data, you may use mysql -u root -p to retrive and the password is code1234


check the retrieval of prices of all related formss like in ordering

what is your proposal solution?

- do not touch the code first, let first plan.